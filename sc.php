<?php

header('Content-Type: application/json; charset=utf-8');

// ─── Dispatch ────────────────────────────────────────────────────────────────

$action = isset($_GET['action']) ? trim($_GET['action']) : '';

if ($action === 'resolve_stream') {
    resoudreFlux();
    exit;
}

if ($action === 'check_streams') {
    verifierFlux();
    exit;
}

if ($action === 'suggestions') {
    suggererRecherche();
    exit;
}

if ($action === 'rechercher') {
    rechercherPistes();
    exit;
}

// ─── Mode 1 : scrape ─────────────────────────────────────────────────────────

$url = isset($_GET['url']) ? trim($_GET['url']) : '';

if (empty($url)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error'   => "Paramètre 'url' manquant. Exemple : ?url=https://soundcloud.com/artiste/titre"
    ]);
    exit;
}

if (!filter_var($url, FILTER_VALIDATE_URL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => "URL invalide : $url"]);
    exit;
}

// Nettoyer l'URL : garder uniquement scheme://host/path (sans query string ni fragment)
$urlParsee = parse_url($url);
$url = ($urlParsee['scheme'] ?? 'https') . '://'
     . ($urlParsee['host']   ?? '')
     . ($urlParsee['path']   ?? '');

// ─── Téléchargement du code source ──────────────────────────────────────────

$context = stream_context_create([
    'http' => [
        'method'          => 'GET',
        'timeout'         => 30,
        'follow_location' => true,
        'max_redirects'   => 5,
        'header'          => implode("\r\n", [
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) '
                . 'AppleWebKit/537.36 (KHTML, like Gecko) '
                . 'Chrome/124.0.0.0 Safari/537.36',
            'Accept-Language: fr-FR,fr;q=0.9,en;q=0.8',
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        ]),
    ],
]);

$html = @file_get_contents($url, false, $context);

if ($html === false) {
    http_response_code(502);
    echo json_encode([
        'success' => false,
        'error'   => "Impossible de récupérer la page. Vérifiez l'URL ou la connexion."
    ]);
    exit;
}

// ─── Extraction de window.__sc_hydration ─────────────────────────────────────

if (!preg_match('/window\.__sc_hydration\s*=\s*(\[.*?\]);/s', $html, $matches)) {
    http_response_code(404);
    echo json_encode([
        'success' => false,
        'error'   => "Données JSON introuvables dans le code source de la page."
    ]);
    exit;
}

$hydratation = json_decode($matches[1], true);

if ($hydratation === null && json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => "JSON invalide : " . json_last_error_msg()]);
    exit;
}

// ─── Extraction ──────────────────────────────────────────────────────────────

$donnees = extraireDonnees($hydratation);

if ($donnees === null) {
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => "Aucune donnée importante n'a pu être extraite."]);
    exit;
}

// ─── Sauvegarde silencieuse ──────────────────────────────────────────────────

$nomFichier   = preg_replace('/[^a-z0-9_-]/', '_', strtolower(parse_url($url, PHP_URL_HOST) . parse_url($url, PHP_URL_PATH)));
$nomFichier   = trim(preg_replace('/_+/', '_', $nomFichier), '_');
$nomFichier   = substr($nomFichier, 0, 80) . '_' . date('Ymd_His') . '.json';
$dossierSauvegarde = __DIR__ . DIRECTORY_SEPARATOR . 'MP3';
if (!is_dir($dossierSauvegarde)) @mkdir($dossierSauvegarde, 0755, true);
@file_put_contents($dossierSauvegarde . DIRECTORY_SEPARATOR . $nomFichier,
    json_encode($donnees, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

// ─── Réponse ─────────────────────────────────────────────────────────────────

echo json_encode([
    'success' => true,
    'url'     => $url,
    'data'    => $donnees
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

exit;


function verifierFlux(): void
{
    $streams   = isset($_GET['streams'])          ? trim($_GET['streams'])          : '';
    $idClient  = isset($_GET['client_id'])         ? trim($_GET['client_id'])         : '';
    $tokenPiste = isset($_GET['track_authorization'])? trim($_GET['track_authorization']): '';

    if (empty($streams) || empty($idClient)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Paramètres manquants.']);
        return;
    }

    $listeUrls = json_decode($streams, true);
    if (!is_array($listeUrls)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'streams doit être un tableau JSON.']);
        return;
    }

    $valides = [];

    foreach ($listeUrls as $element) {
        $urlFlux = $element['url'] ?? '';
        if (empty($urlFlux)) continue;

        $urlApi = $urlFlux
                . '?client_id='           . urlencode($idClient)
                . '&track_authorization=' . urlencode($tokenPiste);

        $contexte = stream_context_create([
            'http' => [
                'method'          => 'GET',
                'timeout'         => 8,
                'follow_location' => true,
                'max_redirects'   => 3,
                'ignore_errors'   => true,
                'header'          => implode("\r\n", [
                    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) '
                        . 'AppleWebKit/537.36 (KHTML, like Gecko) '
                        . 'Chrome/124.0.0.0 Safari/537.36',
                    'Accept: application/json, */*',
                    'Origin: https://soundcloud.com',
                    'Referer: https://soundcloud.com/',
                ]),
            ],
        ]);

        $reponse = @file_get_contents($urlApi, false, $contexte);
        $codeHttp = 0;
        if (isset($http_response_header)) {
            foreach ($http_response_header as $entete) {
                if (preg_match('#HTTP/\S+\s+(\d+)#', $entete, $correspondance)) {
                    $codeHttp = (int)$correspondance[1];
                }
            }
        }

        if ($reponse === false || $codeHttp === 404) continue;

        $json = json_decode($reponse, true);
        if (json_last_error() !== JSON_ERROR_NONE || !isset($json['url'])) continue;

        $valides[] = [
            'index'            => $element['index'],
            'url'              => $json['url'],
            'licenseAuthToken' => $json['licenseAuthToken'] ?? null,
        ];
    }

    echo json_encode(['success' => true, 'valid' => $valides]);
}


function resoudreFlux(): void
{
    $urlFlux  = isset($_GET['stream_url'])         ? trim($_GET['stream_url'])         : '';
    $idClient   = isset($_GET['client_id'])           ? trim($_GET['client_id'])           : '';
    $tokenPiste  = isset($_GET['track_authorization']) ? trim($_GET['track_authorization']) : '';

    if (empty($urlFlux) || empty($idClient)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => "Paramètres stream_url et client_id requis."]);
        return;
    }

    // Construction de l'URL API SoundCloud
    $urlApi = $urlFlux
            . '?client_id='          . urlencode($idClient)
            . '&track_authorization='. urlencode($tokenPiste);

    // Appel depuis le serveur PHP (pas de restriction CORS ici)
    $contexte = stream_context_create([
        'http' => [
            'method'          => 'GET',
            'timeout'         => 15,
            'follow_location' => true,
            'max_redirects'   => 3,
            'header'          => implode("\r\n", [
                'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) '
                    . 'AppleWebKit/537.36 (KHTML, like Gecko) '
                    . 'Chrome/124.0.0.0 Safari/537.36',
                'Accept: application/json, */*',
                'Origin: https://soundcloud.com',
                'Referer: https://soundcloud.com/',
            ]),
        ],
        'ssl' => [
            'verify_peer'      => true,
            'verify_peer_name' => true,
        ],
    ]);

    $reponse = @file_get_contents($urlApi, false, $contexte);

    if ($reponse === false) {
        // Tenter de récupérer le code HTTP
        $codeHttp = 0;
        if (isset($http_response_header)) {
            foreach ($http_response_header as $entete) {
                if (preg_match('#HTTP/\S+\s+(\d+)#', $entete, $correspondance)) {
                    $codeHttp = (int)$correspondance[1];
                }
            }
        }
        http_response_code(502);
        echo json_encode([
            'success'   => false,
            'error'     => "L'API SoundCloud n'a pas répondu (HTTP $codeHttp).",
            'api_url'   => $urlApi,
        ]);
        return;
    }

    $json = json_decode($reponse, true);

    if (json_last_error() !== JSON_ERROR_NONE || !isset($json['url'])) {
        http_response_code(502);
        echo json_encode([
            'success' => false,
            'error'   => "Réponse inattendue de l'API SoundCloud.",
            'raw'     => substr($reponse, 0, 500),
        ]);
        return;
    }

    echo json_encode([
        'success'           => true,
        'url'               => $json['url'],
        'licenseAuthToken'  => $json['licenseAuthToken'] ?? null,
    ]);
}

// ═════════════════════════════════════════════════════════════════════════════
// Extraction des données
// ═════════════════════════════════════════════════════════════════════════════

function extraireDonnees(?array $hydratation): ?array
{
    if (empty($hydratation)) return null;

    $blocs = [];
    foreach ($hydratation as $entree) {
        if (isset($entree['hydratable'])) {
            $blocs[$entree['hydratable']] = $entree['data'] ?? null;
        }
    }

    $resultat = [];

    // ── API Client ────────────────────────────────────────────────────────
    if (isset($blocs['apiClient'])) {
        $resultat['api_client'] = ['id' => $blocs['apiClient']['id'] ?? null];
    }

    // ── Artiste ───────────────────────────────────────────────────────────
    if (isset($blocs['user'])) {
        $artiste = $blocs['user'];
        $resultat['artist'] = [
            'id'               => $artiste['id']               ?? null,
            'username'         => $artiste['username']          ?? null,
            'full_name'        => $artiste['full_name']         ?? null,
            'permalink'        => $artiste['permalink']         ?? null,
            'permalink_url'    => $artiste['permalink_url']     ?? null,
            'description'      => $artiste['description']       ?? null,
            'city'             => $artiste['city']              ?? null,
            'country_code'     => $artiste['country_code']      ?? null,
            'followers_count'  => $artiste['followers_count']   ?? null,
            'followings_count' => $artiste['followings_count']  ?? null,
            'track_count'      => $artiste['track_count']       ?? null,
            'playlist_count'   => $artiste['playlist_count']    ?? null,
            'likes_count'      => $artiste['likes_count']       ?? null,
            'reposts_count'    => $artiste['reposts_count']     ?? null,
            'comments_count'   => $artiste['comments_count']    ?? null,
            'verified'         => $artiste['verified']          ?? null,
            'badges'           => $artiste['badges']            ?? null,
            'avatar_url'       => $artiste['avatar_url']        ?? null,
            'subscription'     => $artiste['creator_subscription']['product']['id'] ?? null,
            'last_modified'    => $artiste['last_modified']     ?? null,
            'urn'              => $artiste['urn']               ?? null,
            'station_urn'      => $artiste['station_urn']       ?? null,
        ];
        if (!empty($u['visuals']['visuals'][0]['visual_url'])) {
            $resultat['artist']['banner_url'] = $artiste['visuals']['visuals'][0]['visual_url'];
        }
    }

    // ── Track ─────────────────────────────────────────────────────────────
    if (isset($blocs['sound'])) {
        $piste = $blocs['sound'];
        $resultat['track'] = [
            'id'                  => $piste['id']                  ?? null,
            'title'               => $piste['title']               ?? null,
            'permalink_url'       => $piste['permalink_url']       ?? null,
            'genre'               => $piste['genre']               ?? null,
            'description'         => $piste['description']         ?? null,
            'duration_ms'         => $piste['duration']            ?? null,
            'duration_human'      => formaterDuree($piste['duration'] ?? 0),
            'created_at'          => $piste['created_at']          ?? null,
            'release_date'        => $piste['release_date']        ?? null,
            'label_name'          => $piste['label_name']          ?? null,
            'monetization'        => $piste['monetization_model']  ?? null,
            'playback_count'      => $piste['playback_count']      ?? null,
            'likes_count'         => $piste['likes_count']         ?? null,
            'reposts_count'       => $piste['reposts_count']       ?? null,
            'comment_count'       => $piste['comment_count']       ?? null,
            'download_count'      => $piste['download_count']      ?? null,
            'artwork_url'         => $piste['artwork_url']         ?? null,
            'waveform_url'        => $piste['waveform_url']        ?? null,
            'urn'                 => $piste['urn']                 ?? null,
            'user_id'             => $piste['user_id']             ?? null,
            'track_authorization' => $piste['track_authorization'] ?? null,
        ];

        if (!empty($piste['publisher_metadata'])) {
            $meta = $piste['publisher_metadata'];
            $resultat['track']['publisher'] = [
                'artist'        => $meta['artist']             ?? null,
                'album_title'   => $meta['album_title']        ?? null,
                'release_title' => $meta['release_title']      ?? null,
                'isrc'          => $meta['isrc']               ?? null,
                'upc_or_ean'    => $meta['upc_or_ean']         ?? null,
                'p_line'        => $meta['p_line_for_display'] ?? null,
                'c_line'        => $meta['c_line_for_display'] ?? null,
            ];
        }

        if (!empty($piste['media']['transcodings'])) {
            $resultat['track']['audio_streams'] = [];
            foreach ($piste['media']['transcodings'] as $transcodage) {
                $resultat['track']['audio_streams'][] = [
                    'url'      => $transcodage['url']                 ?? null,
                    'preset'   => $transcodage['preset']              ?? null,
                    'protocol' => $transcodage['format']['protocol']  ?? null,
                    'mime'     => $transcodage['format']['mime_type'] ?? null,
                    'quality'  => $transcodage['quality']             ?? null,
                ];
            }
        }
    }

    return (!empty($resultat)) ? $resultat : null;
}

function formaterDuree(int $ms): string
{
    $secondes = intdiv($ms, 1000);
    $min      = intdiv($secondes, 60);
    $sec      = $secondes % 60;
    return sprintf('%d:%02d', $min, $sec);
}


// ═════════════════════════════════════════════════════════════════════════════
// Recherche directe : suggestions pendant la frappe + résultats de titres
// (API web de SoundCloud, avec le client_id public de la page d'accueil)
// ═════════════════════════════════════════════════════════════════════════════

/**
 * Nettoie le texte saisi : caractères de contrôle retirés, espaces normalisés, 100 caractères max.
 */
function nettoyerRequeteRecherche($valeur): string
{
    if (!is_string($valeur)) return '';
    $texte = preg_replace('/[\x00-\x1F\x7F]+/', ' ', $valeur);
    $texte = trim((string)preg_replace('/\s+/u', ' ', (string)$texte));
    return function_exists('mb_substr') ? mb_substr($texte, 0, 100) : substr($texte, 0, 100);
}

function creerContexteSoundcloud(int $delai = 8)
{
    return stream_context_create([
        'http' => [
            'method'          => 'GET',
            'timeout'         => $delai,
            'follow_location' => true,
            'max_redirects'   => 3,
            'ignore_errors'   => true,
            'header'          => implode("\r\n", [
                'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) '
                    . 'AppleWebKit/537.36 (KHTML, like Gecko) '
                    . 'Chrome/124.0.0.0 Safari/537.36',
                'Accept: application/json, text/html;q=0.9, */*;q=0.8',
                'Accept-Language: fr-FR,fr;q=0.9,en;q=0.8',
                'Origin: https://soundcloud.com',
                'Referer: https://soundcloud.com/',
            ]),
        ],
    ]);
}

/**
 * GET simple : retourne le code HTTP final et le corps (null si la requête a échoué).
 */
function appelerSoundcloud(string $url, int $delai = 8): array
{
    $corps    = @file_get_contents($url, false, creerContexteSoundcloud($delai));
    $codeHttp = 0;
    if (isset($http_response_header)) {
        foreach ($http_response_header as $entete) {
            if (preg_match('#HTTP/\S+\s+(\d+)#', $entete, $correspondance)) {
                $codeHttp = (int)$correspondance[1];
            }
        }
    }
    return ['code' => $codeHttp, 'corps' => ($corps === false) ? null : $corps];
}

/**
 * Lit le client_id web dans window.__sc_hydration (même source que le mode « scrape »)
 * et le garde une heure dans un fichier temporaire.
 */
function obtenirIdClientWeb(bool $forcer = false): ?string
{
    $fichierCache = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'sc_scraper_client_id.txt';

    if (!$forcer && is_file($fichierCache) && (time() - filemtime($fichierCache)) < 3600) {
        $idCache = trim((string)@file_get_contents($fichierCache));
        if ($idCache !== '') return $idCache;
    }

    foreach (['https://soundcloud.com/', 'https://soundcloud.com/discover'] as $urlPage) {
        $reponse = appelerSoundcloud($urlPage, 15);
        if ($reponse['code'] !== 200 || $reponse['corps'] === null) continue;
        if (!preg_match('/window\.__sc_hydration\s*=\s*(\[.*?\]);/s', $reponse['corps'], $correspondance)) continue;

        $hydratation = json_decode($correspondance[1], true);
        if (!is_array($hydratation)) continue;

        foreach ($hydratation as $entree) {
            $idClient = $entree['data']['id'] ?? null;
            if (($entree['hydratable'] ?? '') === 'apiClient' && is_string($idClient) && $idClient !== '') {
                @file_put_contents($fichierCache, $idClient);
                return $idClient;
            }
        }
    }

    return null;
}

/**
 * Appelle api-v2.soundcloud.com. Si le client_id est refusé (401/403), il est relu une fois.
 * Aucun contournement : si SoundCloud refuse toujours, on renvoie null avec le code HTTP.
 */
function interrogerApiRecherche(string $chemin, array $parametres, ?int &$codeHttp = null): ?array
{
    for ($tentative = 0; $tentative < 2; $tentative++) {
        $idClient = obtenirIdClientWeb($tentative === 1);
        if ($idClient === null) {
            $codeHttp = 0;
            return null;
        }

        $url = 'https://api-v2.soundcloud.com' . $chemin . '?'
             . http_build_query($parametres + ['client_id' => $idClient]);

        $reponse  = appelerSoundcloud($url);
        $codeHttp = $reponse['code'];

        if ($codeHttp === 200 && $reponse['corps'] !== null) {
            $json = json_decode($reponse['corps'], true);
            return is_array($json) ? $json : null;
        }
        if ($codeHttp !== 401 && $codeHttp !== 403) return null;
    }
    return null;
}

/**
 * ?action=suggestions&q=amapiano  →  { success, suggestions: ["amapiano mix", ...] }
 */
function suggererRecherche(): void
{
    $requete = nettoyerRequeteRecherche($_GET['q'] ?? '');

    if (strlen($requete) < 2) {
        echo json_encode(['success' => true, 'suggestions' => []]);
        return;
    }

    $limite   = max(1, min(10, (int)($_GET['limit'] ?? 8)));
    $codeHttp = 0;
    $json     = interrogerApiRecherche('/search/queries', ['q' => $requete, 'limit' => $limite], $codeHttp);

    if ($json === null) {
        http_response_code(502);
        echo json_encode(['success' => false, 'error' => "Suggestions indisponibles (HTTP $codeHttp)."]);
        return;
    }

    $suggestions = [];
    foreach (($json['collection'] ?? []) as $element) {
        if (!is_array($element)) continue;
        $texte = trim((string)($element['output'] ?? $element['query'] ?? ''));
        if ($texte !== '' && !in_array($texte, $suggestions, true)) {
            $suggestions[] = $texte;
        }
    }

    header('Cache-Control: private, max-age=300');
    echo json_encode(['success' => true, 'suggestions' => $suggestions], JSON_UNESCAPED_UNICODE);
}

/**
 * ?action=rechercher&q=amapiano%20mix&limit=12&offset=0  →  { success, pistes: [...], suivant }
 */
function rechercherPistes(): void
{
    $requete = nettoyerRequeteRecherche($_GET['q'] ?? '');

    if (strlen($requete) < 2) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => "Saisissez au moins 2 caractères."]);
        return;
    }

    $limite   = max(1, min(24, (int)($_GET['limit']  ?? 12)));
    $decalage = max(0, min(200, (int)($_GET['offset'] ?? 0)));
    $codeHttp = 0;
    $json     = interrogerApiRecherche('/search/tracks', [
        'q'      => $requete,
        'limit'  => $limite,
        'offset' => $decalage,
    ], $codeHttp);

    if ($json === null) {
        http_response_code(502);
        echo json_encode([
            'success' => false,
            'error'   => "La recherche SoundCloud n'a pas répondu (HTTP $codeHttp). Réessayez dans un instant.",
        ]);
        return;
    }

    $pistes = [];
    foreach (($json['collection'] ?? []) as $piste) {
        if (!is_array($piste) || empty($piste['permalink_url'])) continue;
        $pistes[] = [
            'id'       => $piste['id'] ?? null,
            'titre'    => $piste['title'] ?? '',
            'artiste'  => $piste['user']['username'] ?? ($piste['publisher_metadata']['artist'] ?? ''),
            'url'      => $piste['permalink_url'],
            'pochette' => $piste['artwork_url'] ?? ($piste['user']['avatar_url'] ?? null),
            'duree'    => formaterDuree((int)($piste['duration'] ?? 0)),
            'ecoutes'  => $piste['playback_count'] ?? null,
            'genre'    => $piste['genre'] ?? null,
        ];
    }

    echo json_encode([
        'success' => true,
        'requete' => $requete,
        'pistes'  => $pistes,
        'suivant' => !empty($json['next_href']),
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}