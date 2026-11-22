<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="color-scheme" content="light" />
  <meta name="theme-color" content="#ffffff" />
  <title>SC Scraper – Infos et téléchargement SoundCloud</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500..800&family=Hanken+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <style>
    :root {
      --blanc: #fff; --encre: #1c1c21; --encre-douce: #5f5f6b;
      --trait: #e9e9ee; --trait-fort: #d5d5dd; --voile: #f7f7f9;
      --rouge: #e11d2e; --rouge-fonce: #bf111f; --rouge-pale: #fdeced; --rouge-bord: #f6c9cd;
      --vert: #137a3d; --vert-pale: #e7f6ec; --vert-bord: #bfe5cc;
      --rayon-moyen: 14px; --rayon-image: 18px; --rayon-grand: 28px;
      --police-titre: 'Bricolage Grotesque', 'Segoe UI', system-ui, sans-serif;
      --police-texte: 'Hanken Grotesk', system-ui, -apple-system, 'Segoe UI', sans-serif;
      --courbe: cubic-bezier(.2, .7, .2, 1);
    }
    *, *::before, *::after { box-sizing: border-box; }
    [hidden] { display: none !important; }
    body { margin: 0; min-height: 100vh; background: var(--blanc); color: var(--encre); font: 400 1rem/1.55 var(--police-texte); -webkit-font-smoothing: antialiased; }
    a { color: inherit; } img { display: block; max-width: 100%; } button { font: inherit; } dl { margin: 0; }
    .visuellement-cache { position: absolute; width: 1px; height: 1px; overflow: hidden; clip: rect(0 0 0 0); white-space: nowrap; }
    .page { width: min(1040px, 100% - 2.5rem); margin-inline: auto; padding: 1.5rem 0 5rem; }

    /* ── En-tête ── */
    .entete { display: flex; align-items: center; justify-content: space-between; gap: 1rem; }
    .marque { display: inline-flex; align-items: center; gap: .65rem; font: 700 1.2rem/1 var(--police-titre); letter-spacing: -.01em; text-decoration: none; }
    .marque-logo { display: grid; place-items: center; width: 36px; height: 36px; border-radius: 11px; background: var(--rouge); }
    .entete-note { color: var(--encre-douce); font-size: .9rem; }

    /* ── Hero ── */
    .hero { padding-top: clamp(2.5rem, 7vw, 5rem); transition: padding .55s var(--courbe); }
    .intro { display: grid; grid-template-rows: 1fr; margin-bottom: 2rem; transition: grid-template-rows .55s var(--courbe), opacity .35s, margin .55s var(--courbe); }
    .intro-contenu { min-height: 0; overflow: hidden; }
    .titre-principal { max-width: 17ch; margin: 0; font: 800 clamp(2.3rem, 6.2vw, 4.4rem)/1.02 var(--police-titre); letter-spacing: -.035em; text-wrap: balance; }
    .sous-titre { max-width: 54ch; margin: 1.25rem 0 0; color: var(--encre-douce); font-size: clamp(1.02rem, 1.6vw, 1.2rem); }
    .a-des-resultats .hero { padding-top: 1.75rem; }
    .a-des-resultats .intro { grid-template-rows: 0fr; margin-bottom: 0; opacity: 0; }

    /* ── Boutons ── */
    .bouton { display: inline-flex; align-items: center; justify-content: center; gap: .5rem; border: 0; border-radius: 999px; font-weight: 700; text-decoration: none; white-space: nowrap; cursor: pointer; transition: background-color .18s, color .18s, border-color .18s, transform .1s; }
    .bouton:focus-visible, .bouton-copier:focus-visible { outline: 3px solid rgba(225, 29, 46, .4); outline-offset: 3px; }
    .bouton:disabled { opacity: .55; cursor: not-allowed; }
    .bouton-primaire { padding: .85rem 1.7rem; background: var(--rouge); color: #fff; }
    .bouton-primaire:hover:not(:disabled) { background: var(--rouge-fonce); }
    .bouton-primaire:active:not(:disabled) { transform: scale(.98); }
    .bouton-secondaire { padding: .8rem 1.4rem; background: #fff; color: var(--encre); border: 1.5px solid var(--trait-fort); }
    .bouton-secondaire:hover { border-color: var(--encre); }
    .bouton-contour { min-width: 9rem; padding: .5rem 1.1rem; background: #fff; color: var(--rouge); border: 1.5px solid var(--rouge); font-size: .9rem; }
    .bouton-contour:hover:not(:disabled) { background: var(--rouge); color: #fff; }
    .bouton-libelle { display: inline-flex; align-items: center; gap: .4rem; }
    .bouton-contour .spinner { display: none; }
    .bouton-contour.charge .spinner { display: inline-block; }
    .bouton-contour.charge .bouton-libelle { display: none; }

    /* ── Recherche ── */
    .recherche { display: flex; align-items: center; gap: .5rem; padding: .45rem .45rem .45rem 1.3rem; background: #fff; border: 1.5px solid var(--trait-fort); border-radius: 999px; box-shadow: 0 1px 2px rgba(28, 28, 33, .04); transition: border-color .2s, box-shadow .2s; }
    .recherche:focus-within { border-color: var(--rouge); box-shadow: 0 0 0 4px rgba(225, 29, 46, .14); }
    .recherche-icone { flex: none; color: #9a9aa6; }
    .champ-url { flex: 1; min-width: 0; padding: .7rem .25rem; border: 0; outline: 0; background: transparent; color: var(--encre); font: inherit; font-size: 1.05rem; }
    .champ-url::placeholder { color: #9a9aa6; }
    #bouton-lancer { min-width: 8.5rem; }

    /* ── Ondes (progression) ── */
    .ondes { --hauteur: 96px; display: flex; align-items: center; gap: 3px; height: var(--hauteur); margin-top: 2.25rem; transition: height .55s var(--courbe), margin .55s var(--courbe); }
    .a-des-resultats .ondes { --hauteur: 36px; margin-top: 1.25rem; }
    .onde { flex: 1 1 0; min-width: 2px; height: var(--h); border-radius: 999px; background: var(--trait); transition: background-color .25s; animation: monter .7s var(--courbe) backwards; }
    .onde.active { background: var(--rouge); }
    @keyframes monter { from { transform: scaleY(.04); opacity: 0; } to { transform: none; opacity: 1; } }

    /* ── Statut ── */
    .statut { margin-top: 1.25rem; }
    .chargement { display: flex; align-items: center; gap: .7rem; color: var(--encre-douce); }
    .erreur { display: flex; align-items: flex-start; gap: .75rem; padding: 1rem 1.25rem; background: var(--rouge-pale); border: 1px solid var(--rouge-bord); border-radius: var(--rayon-moyen); color: #8f1019; }
    .erreur svg { flex: none; margin-top: .15rem; }
    .spinner { flex: none; display: inline-block; width: 18px; height: 18px; border: 2px solid var(--trait-fort); border-top-color: var(--rouge); border-radius: 50%; animation: rotation .7s linear infinite; }
    .spinner-blanc { border-color: rgba(255, 255, 255, .4); border-top-color: #fff; }
    @keyframes rotation { to { transform: rotate(360deg); } }

    /* ── Résultats ── */
    .resultats { display: grid; gap: 3rem; margin-top: 2.25rem; animation: apparition .5s var(--courbe) both; }
    @keyframes apparition { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: none; } }
    .vide { padding: 2rem; border: 1px dashed var(--trait-fort); border-radius: var(--rayon-moyen); color: var(--encre-douce); text-align: center; }

    .puces { display: flex; flex-wrap: wrap; gap: .5rem; }
    .puce { display: inline-flex; align-items: center; padding: .2rem .75rem; background: #fff; border: 1px solid var(--trait); border-radius: 999px; color: var(--encre-douce); font-size: .85rem; font-weight: 600; }
    .puce-rouge { background: var(--rouge-pale); border-color: var(--rouge-bord); color: var(--rouge-fonce); }
    .puce-verte { background: var(--vert-pale); border-color: var(--vert-bord); color: var(--vert); }

    /* Titre */
    .piste { --trait: #e3e3e9; display: grid; grid-template-columns: minmax(0, 300px) minmax(0, 1fr); gap: 2rem 2.75rem; padding: clamp(1.25rem, 3vw, 2rem); background: var(--voile); border-radius: var(--rayon-grand); }
    .pochette { width: 100%; aspect-ratio: 1; object-fit: cover; background: #ececf1; border-radius: var(--rayon-image); box-shadow: 0 18px 40px -18px rgba(28, 28, 33, .4); }
    .pochette-vide { display: grid; place-items: center; color: #c3c3cc; }
    .piste-artiste { display: flex; align-items: center; gap: .6rem; margin: 0; color: var(--encre-douce); font-weight: 600; }
    .piste-titre { margin: .6rem 0 1rem; font: 800 clamp(1.8rem, 4vw, 2.9rem)/1.05 var(--police-titre); letter-spacing: -.03em; overflow-wrap: anywhere; text-wrap: balance; }
    .piste-description { max-width: 62ch; margin: 1.1rem 0 0; color: var(--encre-douce); white-space: pre-line; }
    .piste-actions { display: flex; flex-wrap: wrap; gap: .75rem; margin-top: 1.5rem; }
    .piste-details { grid-column: 1 / -1; }

    .stats-piste { display: flex; flex-wrap: wrap; gap: 1.25rem 2.5rem; margin-top: 1.6rem; }
    .stat { display: flex; flex-direction: column-reverse; }
    .stat dt { color: var(--encre-douce); font-size: .85rem; }
    .stat dd { margin: 0; font: 700 1.6rem/1.15 var(--police-titre); letter-spacing: -.02em; font-variant-numeric: tabular-nums; }

    /* Qualités */
    .qualites { display: grid; grid-template-rows: 0fr; transition: grid-template-rows .4s var(--courbe); }
    .qualites.ouvert { grid-template-rows: 1fr; }
    .qualites-interieur { min-height: 0; overflow: hidden; visibility: hidden; transition: visibility 0s .4s; }
    .qualites.ouvert .qualites-interieur { visibility: visible; transition-delay: 0s; }
    .liste-qualites { display: grid; gap: .6rem; padding-top: 1.25rem; }
    .qualite-chargement { display: flex; align-items: center; gap: .7rem; color: var(--encre-douce); }
    .ligne-qualite { display: grid; grid-template-columns: auto 1fr auto; align-items: center; gap: .85rem 1rem; padding: .85rem 1rem; background: #fff; border: 1px solid var(--trait); border-radius: var(--rayon-moyen); }
    .format-pastille { display: grid; place-items: center; width: 48px; height: 48px; background: var(--voile); border-radius: 12px; font: 700 .8rem var(--police-titre); }
    .qualite-titre { font-weight: 700; }
    .qualite-detail { margin-bottom: .4rem; color: var(--encre-douce); font-size: .85rem; overflow-wrap: anywhere; }
    .ligne-qualite .puce { padding: .05rem .6rem; font-size: .78rem; }
    .progression-telechargement { grid-column: 1 / -1; display: flex; align-items: center; gap: .75rem; }
    .progression-fond { flex: 1; height: 6px; overflow: hidden; background: var(--trait); border-radius: 99px; }
    .progression-barre { width: 0; height: 100%; background: var(--rouge); border-radius: 99px; transition: width .15s ease; }
    .progression-etiquette { min-width: 8.5rem; color: var(--encre-douce); font-size: .8rem; text-align: right; font-variant-numeric: tabular-nums; }
    .progression-telechargement.termine .progression-barre { background: var(--vert); }
    .progression-telechargement.termine .progression-etiquette { color: var(--vert); font-weight: 700; }
    .progression-telechargement.echec .progression-barre { background: var(--rouge-fonce); }
    .progression-telechargement.echec .progression-etiquette { color: var(--rouge-fonce); font-weight: 700; }
    .qualite-erreur { grid-column: 1 / -1; margin: 0; color: var(--rouge-fonce); font-size: .85rem; }

    /* Rubriques */
    .rubrique { display: grid; grid-template-columns: 200px minmax(0, 1fr); gap: 1.5rem 3rem; padding-top: 2.25rem; border-top: 1px solid var(--trait); }
    .rubrique-titre { margin: 0; font: 700 1.35rem/1.2 var(--police-titre); letter-spacing: -.02em; }
    .banniere { width: 100%; height: 140px; margin-bottom: 1.5rem; object-fit: cover; border-radius: var(--rayon-moyen); }
    .artiste-entete { display: flex; flex-wrap: wrap; align-items: center; gap: 1rem; }
    .avatar { flex: none; width: 72px; height: 72px; object-fit: cover; background: var(--voile); border: 1px solid var(--trait); border-radius: 50%; }
    .avatar-mini { width: 28px; height: 28px; }
    .artiste-nom { display: flex; align-items: center; gap: .4rem; font: 700 1.35rem/1.2 var(--police-titre); letter-spacing: -.02em; }
    .artiste-pseudo { color: var(--encre-douce); }
    .artiste-entete .puces { flex-basis: 100%; }
    .stats-artiste { display: grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 1.5rem; margin: 1.75rem 0; }

    /* Détails */
    .details { border-top: 1px solid var(--trait); }
    .piste-details { margin-top: .25rem; }
    .detail { display: grid; grid-template-columns: 10.5rem minmax(0, 1fr) auto; align-items: baseline; gap: .25rem 1rem; padding: .8rem 0; border-bottom: 1px solid var(--trait); }
    .detail-cle { color: var(--encre-douce); font-size: .9rem; }
    .detail-valeur { margin: 0; overflow-wrap: anywhere; white-space: pre-line; font-variant-numeric: tabular-nums; }
    .detail-valeur a { color: var(--rouge); font-weight: 600; text-decoration: none; }
    .detail-valeur a:hover { text-decoration: underline; }
    .bouton-copier { display: inline-flex; align-items: center; gap: .3rem; padding: .15rem .7rem; background: #fff; border: 1px solid var(--trait-fort); border-radius: 999px; color: var(--encre-douce); font-size: .8rem; font-weight: 600; cursor: pointer; transition: border-color .15s, color .15s, background-color .15s; }
    .bouton-copier:hover { border-color: var(--rouge); color: var(--rouge); }
    .bouton-copier.copie { background: var(--vert-pale); border-color: var(--vert); color: var(--vert); }

    /* ── Recherche : suggestions et résultats ── */
    .recherche-zone { position: relative; }
    .suggestions { position: absolute; top: calc(100% + 8px); left: 0; right: 0; z-index: 20; margin: 0; padding: .5rem; list-style: none; background: #fff; border: 1px solid var(--trait); border-radius: 24px; box-shadow: 0 24px 48px -16px rgba(28, 28, 33, .28); }
    .suggestion { display: flex; align-items: center; gap: .8rem; padding: .65rem .9rem; border-radius: 14px; cursor: pointer; }
    .suggestion svg { flex: none; color: #9a9aa6; }
    .suggestion.actif { background: var(--voile); }
    .suggestion-saisie { color: var(--encre-douce); }
    /* Cartes de résultats */
    .liste-pistes { display: grid; grid-template-columns: repeat(auto-fill, minmax(min(100%, 232px), 1fr)); gap: 1.1rem; margin: 1.5rem 0 0; padding: 0; list-style: none; }
    .carte-piste { display: flex; flex-direction: column; gap: .9rem; height: 100%; padding: .65rem .65rem .8rem; background: #fff; border: 1px solid var(--trait); border-radius: 22px; transition: border-color .2s, box-shadow .25s; }
    .carte-piste:hover { border-color: var(--rouge-bord); box-shadow: 0 22px 36px -26px rgba(225, 29, 46, .5), 0 6px 14px -10px rgba(28, 28, 33, .16); }
    .carte-piste.en-ecoute { border-color: var(--rouge); box-shadow: 0 0 0 3px rgba(225, 29, 46, .14), 0 22px 36px -26px rgba(225, 29, 46, .55); }

    .carte-pochette { position: relative; isolation: isolate; aspect-ratio: 1; overflow: hidden; background: #ececf1; border-radius: 16px; cursor: pointer; }
    .carte-pochette img { width: 100%; height: 100%; object-fit: cover; transition: transform .6s var(--courbe); }
    .carte-piste:hover .carte-pochette img { transform: scale(1.04); }
    .carte-pochette-vide { display: grid; place-items: center; width: 100%; height: 100%; color: #c3c3cc; }
    .carte-duree { position: absolute; right: .5rem; bottom: .5rem; z-index: 2; padding: .1rem .6rem; background: rgba(255, 255, 255, .92); border-radius: 999px; font: 700 .78rem/1.55 var(--police-titre); font-variant-numeric: tabular-nums; -webkit-backdrop-filter: blur(6px); backdrop-filter: blur(6px); }
    .carte-lecture { position: absolute; inset: 0; z-index: 1; display: grid; place-items: center; background: rgba(28, 28, 33, .3); opacity: 0; transition: opacity .2s; pointer-events: none; }
    .carte-lecture-rond { display: grid; place-items: center; width: 56px; height: 56px; border-radius: 50%; background: var(--rouge); color: #fff; box-shadow: 0 14px 26px -10px rgba(225, 29, 46, .85); transform: scale(.82); transition: transform .3s var(--courbe); }
    .carte-pochette:hover .carte-lecture, .carte-piste.en-ecoute .carte-lecture { opacity: 1; }
    .carte-pochette:hover .carte-lecture-rond, .carte-piste.en-ecoute .carte-lecture-rond { transform: none; }
    .carte-egaliseur { display: none; align-items: flex-end; gap: 3px; height: 20px; }
    .carte-egaliseur i { width: 3.5px; height: 100%; border-radius: 2px; background: #fff; transform: scaleY(.3); transform-origin: bottom; animation: egaliseur .9s ease-in-out infinite; }
    .carte-egaliseur i:nth-child(2) { animation-duration: .65s; animation-delay: -.25s; }
    .carte-egaliseur i:nth-child(3) { animation-duration: 1.05s; animation-delay: -.5s; }
    .carte-egaliseur i:nth-child(4) { animation-duration: .75s; animation-delay: -.1s; }
    .carte-piste.en-lecture .carte-egaliseur { display: inline-flex; }
    .carte-piste.en-lecture .carte-lecture-rond svg { display: none; }

    .carte-texte { display: grid; gap: .15rem; min-width: 0; padding-inline: .35rem; }
    .carte-titre { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; min-height: 2.6em; margin: 0; overflow: hidden; font: 700 1.02rem/1.3 var(--police-titre); letter-spacing: -.01em; overflow-wrap: anywhere; }
    .carte-artiste { margin: 0; overflow: hidden; color: var(--encre-douce); font-size: .9rem; font-weight: 600; text-overflow: ellipsis; white-space: nowrap; }
    .carte-meta { margin: .2rem 0 0; color: #8a8a97; font-size: .82rem; font-variant-numeric: tabular-nums; }
    .carte-actions { display: grid; grid-template-columns: 1fr 1fr; gap: .5rem; margin-top: auto; }
    .carte-actions .bouton { min-width: 0; padding: .62rem .4rem; font-size: .9rem; }
    .carte-piste.en-lecture .bouton-ecouter { background: var(--rouge-pale); color: var(--rouge-fonce); }
    .carte-piste.en-lecture .bouton-ecouter:hover:not(:disabled) { background: #fbd9dc; color: var(--rouge-fonce); }

    .recherche-pied { display: flex; justify-content: center; margin-top: 1.5rem; }
    .bouton-retour { justify-self: start; margin-bottom: -1.5rem; padding: .45rem 1rem .45rem .7rem; background: #fff; border: 1.5px solid var(--trait-fort); color: var(--encre); font-size: .9rem; }
    .bouton-retour:hover { border-color: var(--encre); }

    /* ── Lecteur à forme d'onde ── */
    .lecteur {
      --onde-reste: #e1e1e9; --onde-memoire: #cdcdd8; --rouge-clair: #f4606c;
      grid-column: 1 / -1; position: relative;
      display: grid; grid-template-columns: auto minmax(0, 1fr) auto;
      grid-template-areas: "lecture temps outils" "bas bas bas";
      align-items: center; gap: 2rem 1.35rem;
      padding: 1.35rem 1.6rem 1.5rem; background: #fff; border: 1px solid var(--trait); border-radius: 24px;
      box-shadow: 0 1px 0 rgba(28, 28, 33, .03), 0 28px 44px -30px rgba(225, 29, 46, .34), 0 14px 26px -18px rgba(28, 28, 33, .2);
    }
    .lecteur-lecture { grid-area: lecture; }
    .lecteur-temps  { grid-area: temps; }
    .lecteur-outils { grid-area: outils; }
    .lecteur-bas    { grid-area: bas; display: grid; gap: .7rem; }

    .lecteur-bouton { position: relative; isolation: isolate; display: grid; place-items: center; width: 64px; height: 64px; padding: 0; border: 0; border-radius: 50%; background: var(--rouge); color: #fff; cursor: pointer; box-shadow: 0 14px 26px -10px rgba(225, 29, 46, .7), inset 0 1px 0 rgba(255, 255, 255, .22); transition: background-color .18s, transform .12s var(--courbe), box-shadow .2s; }
    .lecteur-bouton:hover { background: var(--rouge-fonce); transform: scale(1.05); }
    .lecteur-bouton:active { transform: scale(.95); }
    .lecteur-bouton:focus-visible { outline: 3px solid rgba(225, 29, 46, .4); outline-offset: 4px; }
    .lecteur-bouton:disabled { opacity: .5; cursor: not-allowed; transform: none; }
    .lecteur-bouton svg { grid-area: 1 / 1; }
    .lecteur-bouton .icone-lecture { margin-left: 2px; }
    .lecteur-bouton .icone-pause, .lecteur-bouton .spinner { display: none; }
    .lecteur.en-lecture .lecteur-bouton .icone-lecture { display: none; }
    .lecteur.en-lecture .lecteur-bouton .icone-pause { display: block; }
    .lecteur.attente .lecteur-bouton .icone-lecture, .lecteur.attente .lecteur-bouton .icone-pause { display: none; }
    .lecteur.attente .lecteur-bouton .spinner { display: block; grid-area: 1 / 1; width: 26px; height: 26px; border-width: 3px; }
    .lecteur-bouton-halo { position: absolute; inset: 0; z-index: -1; border-radius: 50%; background: var(--rouge); opacity: 0; pointer-events: none; }
    .lecteur.en-lecture .lecteur-bouton-halo { animation: halo 2.1s var(--courbe) infinite; }
    @keyframes halo { 0% { transform: scale(1); opacity: .38; } 100% { transform: scale(1.42); opacity: 0; } }

    .lecteur-temps { display: flex; align-items: baseline; gap: .55rem; min-width: 0; }
    .lecteur-temps-courant { font: 800 clamp(1.75rem, 3.4vw, 2.25rem)/1 var(--police-titre); letter-spacing: -.03em; font-variant-numeric: tabular-nums; }
    .lecteur-temps-total { color: var(--encre-douce); font-weight: 600; font-variant-numeric: tabular-nums; }
    .egaliseur { align-self: center; display: inline-flex; align-items: flex-end; gap: 2px; height: 18px; margin-left: .45rem; opacity: 0; transition: opacity .25s; }
    .lecteur.en-lecture .egaliseur { opacity: 1; }
    .egaliseur i { width: 3px; height: 100%; border-radius: 2px; background: var(--rouge); transform: scaleY(.28); transform-origin: bottom; }
    .lecteur.en-lecture .egaliseur i { animation: egaliseur .9s ease-in-out infinite; }
    .lecteur.en-lecture .egaliseur i:nth-child(2) { animation-duration: .65s; animation-delay: -.25s; }
    .lecteur.en-lecture .egaliseur i:nth-child(3) { animation-duration: 1.05s; animation-delay: -.5s; }
    .lecteur.en-lecture .egaliseur i:nth-child(4) { animation-duration: .75s; animation-delay: -.1s; }
    @keyframes egaliseur { 0%, 100% { transform: scaleY(.28); } 50% { transform: scaleY(1); } }

    .lecteur-outils { display: flex; align-items: center; gap: .5rem; }
    .lecteur-outil { display: inline-grid; place-items: center; min-width: 40px; height: 40px; padding: 0; background: #fff; border: 1.5px solid var(--trait-fort); border-radius: 999px; color: var(--encre); cursor: pointer; transition: border-color .15s, color .15s, background-color .15s, transform .1s; }
    .lecteur-outil:hover { border-color: var(--rouge); color: var(--rouge); }
    .lecteur-outil:active { transform: scale(.94); }
    .lecteur-outil:focus-visible { outline: 3px solid rgba(225, 29, 46, .4); outline-offset: 3px; }
    .lecteur-vitesse { min-width: 3.5rem; padding: 0 .8rem; font-size: .9rem; font-weight: 700; font-variant-numeric: tabular-nums; }
    .lecteur-volume { display: flex; align-items: center; gap: .35rem; margin-left: .35rem; }
    .lecteur-volume .vol-haut, .lecteur-volume .vol-bas, .lecteur-volume .vol-muet { display: none; }
    .lecteur-volume[data-niveau="haut"] .vol-haut, .lecteur-volume[data-niveau="bas"] .vol-bas, .lecteur-volume[data-niveau="muet"] .vol-muet { display: block; }
    .lecteur-curseur { --p: 100%; -webkit-appearance: none; appearance: none; width: 92px; height: 22px; margin: 0; background: transparent; cursor: pointer; }
    .lecteur-curseur::-webkit-slider-runnable-track { height: 4px; border-radius: 999px; background: linear-gradient(to right, var(--rouge) var(--p), var(--trait-fort) var(--p)); }
    .lecteur-curseur::-webkit-slider-thumb { -webkit-appearance: none; box-sizing: border-box; width: 14px; height: 14px; margin-top: -5px; border: 2px solid var(--rouge); border-radius: 50%; background: #fff; box-shadow: 0 1px 3px rgba(28, 28, 33, .25); transition: transform .15s; }
    .lecteur-curseur:hover::-webkit-slider-thumb { transform: scale(1.18); }
    .lecteur-curseur::-moz-range-track { height: 4px; border-radius: 999px; background: var(--trait-fort); }
    .lecteur-curseur::-moz-range-progress { height: 4px; border-radius: 999px; background: var(--rouge); }
    .lecteur-curseur::-moz-range-thumb { box-sizing: border-box; width: 14px; height: 14px; border: 2px solid var(--rouge); border-radius: 50%; background: #fff; }
    .lecteur-curseur:focus-visible { outline: 3px solid rgba(225, 29, 46, .4); outline-offset: 3px; border-radius: 999px; }

    .lecteur-onde { position: relative; height: 116px; border-radius: 14px; cursor: pointer; touch-action: pan-y; user-select: none; -webkit-tap-highlight-color: transparent; }
    .lecteur-onde canvas { display: block; width: 100%; height: 100%; }
    .lecteur-onde:focus-visible { outline: 3px solid rgba(225, 29, 46, .4); outline-offset: 5px; }
    .lecteur-onde.chargement::after { content: ''; position: absolute; inset: 0; background: linear-gradient(100deg, transparent 30%, rgba(255, 255, 255, .8) 50%, transparent 70%) 0 0 / 250% 100%; animation: balayage 1.5s linear infinite; pointer-events: none; }
    @keyframes balayage { from { background-position: 150% 0; } to { background-position: -50% 0; } }
    .lecteur-infobulle { position: absolute; top: -4px; left: 0; z-index: 2; transform: translate(-50%, -100%); padding: .2rem .65rem; background: var(--encre); border-radius: 9px; color: #fff; font: 700 .82rem/1.4 var(--police-titre); font-variant-numeric: tabular-nums; white-space: nowrap; pointer-events: none; }
    .lecteur-infobulle::after { content: ''; position: absolute; left: 50%; top: 100%; margin-left: -5px; border: 5px solid transparent; border-top-color: var(--encre); }
    .lecteur-message { margin: 0; color: var(--encre-douce); font-size: .88rem; }
    .lecteur-message.erreur-lecture { color: var(--rouge-fonce); font-weight: 600; }

    @media (max-width: 640px) {
      .lecteur { grid-template-columns: auto minmax(0, 1fr); grid-template-areas: "lecture temps" "bas bas" "outils outils"; gap: 1.8rem 1rem; padding: 1.1rem 1.1rem 1.2rem; border-radius: 20px; }
      .lecteur-bouton { width: 56px; height: 56px; }
      .lecteur-onde { height: 92px; }
      .lecteur-outils { justify-content: space-between; }
      .lecteur-volume { display: none; }
    }

    /* ── Lecteur ancré en bas de page ── */
    .lecteur.lecteur-dock {
      position: fixed; inset: auto 0 max(.75rem, env(safe-area-inset-bottom)); z-index: 40;
      width: min(1040px, 100% - 1.5rem); margin-inline: auto; grid-column: auto;
      grid-template-columns: minmax(0, 1.15fr) auto minmax(5.5rem, auto) auto auto;
      grid-template-areas: "info transport temps outils fermer" "onde onde onde onde onde";
      gap: .9rem 1.1rem; padding: .85rem 1rem 1rem; border-radius: 26px;
      box-shadow: 0 -1px 0 rgba(28, 28, 33, .03), 0 30px 60px -20px rgba(225, 29, 46, .4), 0 18px 36px -18px rgba(28, 28, 33, .3);
      animation: dock-entree .5s var(--courbe) both;
    }
    @keyframes dock-entree { from { opacity: 0; transform: translateY(28px); } to { opacity: 1; transform: none; } }
    .dock-info { grid-area: info; display: flex; align-items: center; gap: .8rem; min-width: 0; }
    .dock-pochette { flex: none; width: 56px; height: 56px; object-fit: cover; background: #ececf1; border-radius: 14px; box-shadow: 0 10px 20px -10px rgba(28, 28, 33, .5); }
    .dock-pochette-vide { display: grid; place-items: center; color: #c3c3cc; }
    .dock-texte { min-width: 0; }
    .dock-titre, .dock-artiste { margin: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .dock-titre { font: 700 1rem/1.3 var(--police-titre); letter-spacing: -.01em; }
    .dock-artiste { color: var(--encre-douce); font-size: .88rem; }
    .dock-transport { grid-area: transport; display: flex; align-items: center; gap: .6rem; }
    .lecteur-dock .lecteur-bouton { width: 52px; height: 52px; }
    .lecteur-dock .lecteur-temps { grid-area: temps; }
    .lecteur-dock .lecteur-temps-courant { font-size: 1.3rem; }
    .lecteur-dock .lecteur-temps-total { font-size: .9rem; }
    .lecteur-dock .lecteur-outils { grid-area: outils; }
    .lecteur-dock .dock-fermer { grid-area: fermer; }
    .lecteur-dock .lecteur-bas { grid-area: onde; }
    .lecteur-dock .lecteur-onde { height: 88px; }

    .notification { position: fixed; inset: 1rem 0 auto; z-index: 60; display: flex; align-items: center; gap: .6rem; width: fit-content; max-width: min(92vw, 440px); margin-inline: auto; padding: .75rem 1.1rem; background: #fff; border: 1px solid var(--rouge-bord); border-radius: 14px; color: #8f1019; font-weight: 600; box-shadow: 0 18px 36px -18px rgba(28, 28, 33, .35); animation: apparition .3s var(--courbe) both; }
    .notification svg { flex: none; }
    body.avec-dock .page { padding-bottom: 15rem; }

    @media (max-width: 860px) {
      .lecteur.lecteur-dock { grid-template-columns: minmax(0, 1fr) auto auto; grid-template-areas: "info transport fermer" "onde onde onde" "temps outils outils"; gap: .8rem; padding: .75rem .85rem .85rem; border-radius: 22px; }
      .lecteur-dock .lecteur-outils { justify-content: flex-end; }
      .lecteur-dock .lecteur-onde { height: 68px; }
    }
    @media (max-width: 560px) {
      .dock-transport .lecteur-outil { display: none; }
      .dock-pochette { width: 46px; height: 46px; border-radius: 12px; }
      .liste-pistes { grid-template-columns: 1fr; gap: .75rem; }
      .carte-piste { display: grid; grid-template-columns: 92px minmax(0, 1fr); grid-template-areas: "pochette texte" "actions actions"; align-items: center; gap: .65rem .9rem; }
      .carte-pochette { grid-area: pochette; border-radius: 14px; }
      .carte-lecture-rond { width: 40px; height: 40px; }
      .carte-duree { right: .35rem; bottom: .35rem; padding-inline: .45rem; font-size: .7rem; }
      .carte-texte { grid-area: texte; padding-inline: 0; }
      .carte-titre { min-height: 0; }
      .carte-actions { grid-area: actions; }
    }

    @media (max-width: 860px) {
      .piste { grid-template-columns: 1fr; }
      .pochette { max-width: 320px; }
      .rubrique { grid-template-columns: 1fr; gap: 1rem; }
    }
    @media (max-width: 640px) {
      .entete-note, .recherche-icone { display: none; }
      .recherche { padding-left: .6rem; }
      #bouton-lancer { min-width: 7.5rem; padding-inline: 1.2rem; }
      .detail { grid-template-columns: minmax(0, 1fr) auto; }
      .detail-cle { grid-column: 1 / -1; }
      .ligne-qualite { grid-template-columns: auto 1fr; }
      .bouton-telecharger { grid-column: 1 / -1; width: 100%; }
    }
    @media (prefers-reduced-motion: reduce) {
      *, *::before, *::after { animation-duration: .01ms !important; animation-delay: 0s !important; transition-duration: .01ms !important; }
    }
  </style>
</head>
<body>
  <div class="page" id="page">

    <header class="entete">
      <a class="marque" href="./">
        <span class="marque-logo">
          <svg width="22" height="22" viewBox="0 0 22 22" fill="#fff" aria-hidden="true">
            <rect x="3" y="8" width="3" height="6" rx="1.5"/><rect x="8" y="4" width="3" height="14" rx="1.5"/>
            <rect x="13" y="7" width="3" height="8" rx="1.5"/><rect x="18" y="9.5" width="3" height="3" rx="1.5"/>
          </svg>
        </span>
        SC Scraper
      </a>
      <span class="entete-note">Extracteur de métadonnées SoundCloud</span>
    </header>

    <main class="hero">
      <div class="intro">
        <div class="intro-contenu">
          <h1 class="titre-principal">Cherchez un titre SoundCloud, obtenez toutes ses infos.</h1>
          <p class="sous-titre">Tapez un titre ou un artiste, ou collez une adresse SoundCloud&nbsp;: écoute avec forme d’onde, statistiques, métadonnées éditeur et téléchargement audio.</p>
        </div>
      </div>

      <div class="recherche-zone">
        <form class="recherche" id="formulaire" novalidate role="search">
          <svg class="recherche-icone" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="9" cy="9" r="5.5"/><path d="M13.5 13.5L17 17"/></svg>
          <label class="visuellement-cache" for="champ-url">Titre, artiste ou adresse SoundCloud</label>
          <input id="champ-url" class="champ-url" type="text" placeholder="Titre, artiste ou lien SoundCloud" autocomplete="off" autocapitalize="off" spellcheck="false" enterkeyhint="search" role="combobox" aria-autocomplete="list" aria-expanded="false" aria-controls="liste-suggestions" />
          <button id="bouton-lancer" class="bouton bouton-primaire" type="submit">Rechercher</button>
        </form>
        <ul class="suggestions" id="liste-suggestions" role="listbox" aria-label="Suggestions de recherche" hidden></ul>
      </div>

      <div class="ondes" id="ondes" aria-hidden="true"></div>

      <div class="statut" id="zone-statut" hidden>
        <div class="chargement" id="boite-chargement" role="status" hidden>
          <span class="spinner"></span><span id="message-chargement">Récupération en cours…</span>
        </div>
        <div class="erreur" id="boite-erreur" role="alert" hidden></div>
      </div>

      <section class="resultats" id="resultats-recherche" aria-label="Résultats de la recherche" hidden></section>
      <div class="resultats" id="resultats" hidden></div>
    </main>
  </div>

  <div class="notification" id="notification" role="status" hidden></div>
  <section class="lecteur lecteur-dock" id="dock-lecteur" aria-label="Lecteur audio" hidden></section>

  <script>
  // ── Utilitaires ──────────────────────────────────────────────────────────
  const parId    = id => document.getElementById(id);
  const formater = n => (n == null ? '—' : Number(n).toLocaleString('fr-FR'));
  const echapper = s => s == null ? '' : String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
  const couper   = (texte, max) => texte.length > max ? texte.slice(0, max).trimEnd() + '…' : texte;
  const estUrlHttp = u => /^https?:\/\//i.test(u || '');
  function formaterDate(iso) {
    const date = new Date(iso);
    return isNaN(date) ? String(iso) : date.toLocaleDateString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric' });
  }

  const CHEMINS_ICONES = {
    telecharger: '<path d="M10 3v9.5M6 9l4 4 4-4M4 16h12"/>',
    haut:        '<path d="M5 12l5-5 5 5"/>',
    externe:     '<path d="M8 4H5a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1v-3M11 4h5v5M16 4l-7 7"/>',
    copier:      '<rect x="7" y="7" width="9" height="9" rx="2"/><path d="M13 7V5a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1v7a1 1 0 0 0 1 1h2"/>',
    alerte:      '<circle cx="10" cy="10" r="8"/><path d="M10 6v5M10 13.8v.2"/>',
    note:        '<path d="M8 15V5l8-2v10"/><circle cx="6" cy="15" r="2.2"/><circle cx="14" cy="13" r="2.2"/>',
    loupe:       '<circle cx="9" cy="9" r="5.5"/><path d="M13.5 13.5L17 17"/>',
    gauche:      '<path d="M12 5l-5 5 5 5"/>',
    lire:        '<path d="M6.5 4.5v11l9-5.5z" fill="currentColor"/>',
    pause:       '<path d="M7 4.5v11M13 4.5v11" stroke-width="2.6"/>',
    fermer:      '<path d="M5 5l10 10M15 5L5 15"/>',
  };
  const icone = (nom, taille = 18) =>
    `<svg width="${taille}" height="${taille}" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">${CHEMINS_ICONES[nom]}</svg>`;
  const ICONE_VERIFIE = '<svg width="20" height="20" viewBox="0 0 20 20" role="img"><title>Compte vérifié</title><circle cx="10" cy="10" r="10" fill="currentColor"/><path d="M5.8 10.4l2.8 2.8 5.6-6" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';

  function ligneKv(cle, valeur, estLien = false, copiable = false) {
    if (valeur == null || valeur === '') return '';
    const texte = String(valeur);
    const affichage = estLien && estUrlHttp(texte)
      ? `<a href="${echapper(texte)}" target="_blank" rel="noopener">${echapper(texte)}</a>`
      : echapper(texte);
    const boutonCopie = copiable
      ? `<button type="button" class="bouton-copier" data-copie="${echapper(texte)}">${icone('copier', 14)}<span>Copier</span></button>`
      : '';
    return `<div class="detail"><dt class="detail-cle">${echapper(cle)}</dt><dd class="detail-valeur">${affichage}</dd>${boutonCopie}</div>`;
  }
  const htmlStat = (libelle, valeur) => `<div class="stat"><dt>${libelle}</dt><dd>${valeur}</dd></div>`;

  // ── Ondes : décor du hero et indicateur de progression ───────────────────
  function generateurAleatoire(graine) {
    return function () {
      graine |= 0; graine = graine + 0x6D2B79F5 | 0;
      let t = Math.imul(graine ^ graine >>> 15, 1 | graine);
      t = t + Math.imul(t ^ t >>> 7, 61 | t) ^ t;
      return ((t ^ t >>> 14) >>> 0) / 4294967296;
    };
  }

  let pourcentageOndes = 0;
  let ondesAnimees = false;

  function mettreAJourOndes(pourcentage) {
    pourcentageOndes = pourcentage;
    const barres = parId('ondes').children;
    const limite = Math.round(barres.length * pourcentage / 100);
    for (let i = 0; i < barres.length; i++) barres[i].classList.toggle('active', i < limite);
  }

  function genererOndes() {
    const conteneur = parId('ondes');
    const nombre = Math.max(24, Math.floor(conteneur.clientWidth / 9));
    if (nombre === conteneur.children.length) return;
    const aleatoire = generateurAleatoire(2718);
    const fragment = document.createDocumentFragment();
    for (let i = 0; i < nombre; i++) {
      const hauteur = Math.min(100, 16 + 84 * Math.abs(0.55 * Math.sin(i * 0.23) + 0.32 * Math.sin(i * 0.071 + 1.3)) * (0.4 + 0.6 * aleatoire()));
      const barre = document.createElement('span');
      barre.className = 'onde';
      barre.style.setProperty('--h', hauteur.toFixed(1) + '%');
      if (ondesAnimees) barre.style.animation = 'none';
      else barre.style.animationDelay = Math.round(i / nombre * 900) + 'ms';
      fragment.appendChild(barre);
    }
    conteneur.replaceChildren(fragment);
    ondesAnimees = true;
    mettreAJourOndes(pourcentageOndes);
  }

  // ── Animation de progression ─────────────────────────────────────────────
  let minuteurProgression = null;
  let minuteurRetour = null;
  function demarrerProgression() {
    clearInterval(minuteurProgression);
    clearTimeout(minuteurRetour);
    mettreAJourOndes(0);
    let valeur = 0;
    minuteurProgression = setInterval(() => {
      valeur = Math.min(valeur + Math.random() * 8, 88);
      mettreAJourOndes(valeur);
    }, 250);
  }
  function terminerProgression(succes = true) {
    clearInterval(minuteurProgression);
    mettreAJourOndes(succes ? 100 : 0);
    if (succes) minuteurRetour = setTimeout(() => mettreAJourOndes(0), 900);
  }

  // ── États de l'interface ─────────────────────────────────────────────────
  const ressembleAUneUrl = t => /^https?:\/\//i.test(t) || /^((www|m|on)\.)?soundcloud\.com(\/|$)/i.test(t);
  const libelleLancement = () => ressembleAUneUrl(parId('champ-url').value.trim()) ? 'Analyser' : 'Rechercher';
  function majLibelleLancement() {
    const bouton = parId('bouton-lancer');
    if (!bouton.disabled) bouton.textContent = libelleLancement();
  }
  function definirLancement(enCours, mode = 'analyse') {
    const bouton = parId('bouton-lancer');
    bouton.disabled = enCours;
    bouton.textContent = enCours ? (mode === 'recherche' ? 'Recherche…' : 'Analyse…') : libelleLancement();
  }
  function masquerResultats() {
    detruireLecteur();
    parId('resultats').hidden = true;
    parId('resultats-recherche').hidden = true;
  }
  function afficherChargement(msg = 'Récupération en cours…', mode = 'analyse') {
    fermerSuggestions();
    parId('zone-statut').hidden = false;
    parId('boite-chargement').hidden = false;
    parId('boite-erreur').hidden = true;
    parId('message-chargement').textContent = msg;
    masquerResultats();
    definirLancement(true, mode);
    demarrerProgression();
  }
  function afficherErreur(msg) {
    parId('zone-statut').hidden = false;
    parId('boite-chargement').hidden = true;
    const boite = parId('boite-erreur');
    boite.hidden = false;
    boite.innerHTML = icone('alerte', 20) + '<span>' + echapper(msg) + '</span>';
    masquerResultats();
    definirLancement(false);
    terminerProgression(false);
  }
  function masquerStatut() {
    parId('zone-statut').hidden = true;
    parId('boite-chargement').hidden = true;
    parId('boite-erreur').hidden = true;
  }

  // ── Requête principale ───────────────────────────────────────────────────
  let _donneesActuelles = null;
  let _fluxEnAttente = null;

  async function lancer() {
    let url = parId('champ-url').value.trim();
    if (!url) { afficherErreur('Veuillez entrer une URL SoundCloud.'); return; }

    if (!/^https?:\/\//i.test(url)) url = 'https://' + url;

    // Nettoyer l'URL : garder uniquement scheme://host/path
    try {
      const u = new URL(url);
      url = u.origin + u.pathname;
    } catch (_) { /* URL invalide, laisse sc.php gérer l'erreur */ }

    afficherChargement('Récupération en cours…');

    try {
      const reponse  = await fetch('./sc.php?url=' + encodeURIComponent(url));
      const resultat = await reponse.json();

      if (!resultat.success) {
        throw new Error(resultat.error || 'Erreur inconnue du serveur');
      }

      _donneesActuelles = resultat.data;

      terminerProgression(true);
      masquerStatut();
      definirLancement(false);
      afficherResultats(_donneesActuelles);

    } catch (e) {
      afficherErreur('Erreur : ' + e.message);
    }
  }

  // ── Téléchargement côté client : fetch M3U8 → segments → Blob → <a download>
  async function demarrerTelechargement(bouton, urlM3u8, jetonLicence, profil, titrePiste) {
    bouton.classList.add('charge');
    bouton.disabled = true;

    const ligne       = bouton.closest('.ligne-qualite');
    const enveloppe   = ligne.querySelector('.progression-telechargement');
    const barre       = enveloppe.querySelector('.progression-barre');
    const etiquette   = enveloppe.querySelector('.progression-etiquette');
    const boiteErreur = ligne.querySelector('.qualite-erreur');

    enveloppe.hidden = false;
    enveloppe.classList.remove('termine', 'echec');
    boiteErreur.hidden = true;

    function mettreAJourProgression(actuel, total, phase) {
      const pct = total > 0 ? Math.round((actuel / total) * 100) : 0;
      barre.style.width = pct + '%';
      etiquette.textContent = phase === 'init' ? 'Préparation…'
                            : phase === 'done' ? '✓ Enregistré'
                            : phase === 'blob' ? 'Finalisation…'
                            : `${pct}\u00A0% · segment ${actuel}/${total}`;
    }

    try {
      // Récupérer la playlist M3U8
      mettreAJourProgression(0, 1, 'init');
      const reponseM3u8 = await fetch(urlM3u8);
      if (!reponseM3u8.ok) throw new Error('Impossible de récupérer la playlist M3U8');
      const texteM3u8 = await reponseM3u8.text();

      // Extraire les URLs des segments (.m4s / .ts)
      const urlBase    = urlM3u8.substring(0, urlM3u8.lastIndexOf('/') + 1);
      const lignes     = texteM3u8.split('\n').map(l => l.trim()).filter(Boolean);
      const ligneInit  = lignes.find(l => l.startsWith('#EXT-X-MAP:URI='));
      const lignesSegs = lignes.filter(l => !l.startsWith('#') && l.length > 0);

      if (lignesSegs.length === 0) throw new Error('Aucun segment trouvé dans la playlist');

      const resoudre = u => u.startsWith('http') ? u : urlBase + u;
      const morceaux = [];
      const total    = lignesSegs.length;

      // Segment init
      if (ligneInit) {
        const urlInit = ligneInit.match(/URI="([^"]+)"/)?.[1];
        if (urlInit) {
          const r = await fetch(resoudre(urlInit));
          if (!r.ok) throw new Error('Échec du segment init');
          morceaux.push(await r.arrayBuffer());
        }
      }

      // Segments audio
      for (let i = 0; i < total; i++) {
        mettreAJourProgression(i + 1, total, 'seg');
        const r = await fetch(resoudre(lignesSegs[i]));
        if (!r.ok) throw new Error(`Échec segment ${i}`);
        morceaux.push(await r.arrayBuffer());
      }

      // Assemblage
      mettreAJourProgression(total, total, 'blob');
      const ext      = profil.includes('opus') ? 'opus' : 'm4a';
      const typeMime = ext === 'opus' ? 'audio/ogg' : 'audio/mp4';
      const blob     = new Blob(morceaux, { type: typeMime });
      const urlBlob  = URL.createObjectURL(blob);
      const lien     = document.createElement('a');
      lien.href      = urlBlob;
      lien.download  = (titrePiste || 'track').replace(/[^a-z0-9 \-_]/gi, '_')
                     + '_' + profil + '.' + ext;
      document.body.appendChild(lien);
      lien.click();
      document.body.removeChild(lien);
      setTimeout(() => URL.revokeObjectURL(urlBlob), 5000);
      mettreAJourProgression(total, total, 'done');
      enveloppe.classList.add('termine');

    } catch (e) {
      enveloppe.classList.add('echec');
      etiquette.textContent = '✗ Échec';
      boiteErreur.textContent = 'Erreur lors du téléchargement : ' + e.message;
      boiteErreur.hidden = false;
    } finally {
      bouton.classList.remove('charge');
      bouton.disabled = false;
    }
  }

  // ── Liste des qualités ───────────────────────────────────────────────────
  function decrireProfil(profil, mime) {
    const p = profil.toLowerCase(), m = mime.toLowerCase();
    let format = 'Audio';
    if (p.includes('opus') || m.includes('opus') || m.includes('ogg')) format = 'Opus';
    else if (p.includes('mp3') || m.includes('mpeg'))                   format = 'MP3';
    else if (p.includes('aac') || m.includes('mp4') || m.includes('aac')) format = 'AAC';
    const debit = p.match(/(\d+)k/);
    return { format, debit: debit ? debit[1] + '\u00A0kbit/s' : null };
  }

  function construireLigneQualite(v, s, titre) {
    const profil    = s.preset   || '?';
    const mime      = s.mime     || '?';
    const protocole = s.protocol || '?';
    const qualite   = s.quality  || '?';
    const { format, debit } = decrireProfil(profil, mime);
    const estChiffre = protocole.includes('encrypted');
    const libelleQualite = qualite === 'hq' ? 'Haute qualité' : qualite === 'sq' ? 'Standard' : qualite;

    const ligne = document.createElement('div');
    ligne.className = 'ligne-qualite';
    ligne.innerHTML = `
      <div class="format-pastille">${echapper(format)}</div>
      <div>
        <div class="qualite-titre">${echapper(format)}${debit ? ' · ' + debit : ''}</div>
        <div class="qualite-detail">${echapper(profil)} · ${echapper(mime)} · ${echapper(protocole).toUpperCase()}</div>
        <div class="puces">
          <span class="puce ${qualite === 'hq' ? 'puce-rouge' : ''}">${echapper(libelleQualite)}</span>
          <span class="puce ${estChiffre ? 'puce-rouge' : 'puce-verte'}">${estChiffre ? 'Chiffré' : 'Ouvert'}</span>
        </div>
      </div>
      <button type="button" class="bouton bouton-contour bouton-telecharger">
        <span class="spinner" aria-hidden="true"></span>
        <span class="bouton-libelle">${icone('telecharger', 16)}Télécharger</span>
      </button>
      <div class="progression-telechargement" hidden>
        <div class="progression-fond"><div class="progression-barre"></div></div>
        <span class="progression-etiquette">0\u00A0%</span>
      </div>
      <p class="qualite-erreur" hidden></p>`;

    // Attacher le handler avec l'URL M3U8 déjà résolue
    ligne.querySelector('.bouton-telecharger').addEventListener('click', function () {
      demarrerTelechargement(this, v.url, v.licenseAuthToken, profil, titre);
    });
    return ligne;
  }

  async function chargerQualites() {
    const liste = parId('liste-qualites');
    const { flux, idClient, authPiste, titre } = _fluxEnAttente;

    // Préparer le tableau index+url pour le proxy
    const donnees = flux.map((s, i) => ({ index: i, url: s.url }));

    try {
      const reponse = await fetch(
        './sc.php?action=check_streams'
        + '&client_id='           + encodeURIComponent(idClient)
        + '&track_authorization=' + encodeURIComponent(authPiste)
        + '&streams='             + encodeURIComponent(JSON.stringify(donnees))
      );
      const resultat = await reponse.json();

      parId('chargement-qualites')?.remove();

      if (!resultat.success || !resultat.valid.length) {
        liste.innerHTML = '<p class="qualite-erreur">Aucune qualité disponible pour ce titre.</p>';
        return;
      }
      resultat.valid.forEach(v => liste.appendChild(construireLigneQualite(v, flux[v.index], titre)));

    } catch (e) {
      liste.innerHTML = `<p class="qualite-erreur">Erreur : ${echapper(e.message)}</p>`;
    }
  }

  const libelleBascule = ouvert => ouvert
    ? icone('haut') + 'Masquer les qualités'
    : icone('telecharger') + 'Télécharger l’audio';

  function initialiserTelechargement() {
    const boutonBascule = parId('bouton-bascule');
    const panneau       = parId('panneau-qualites');
    if (!boutonBascule || !panneau) return;
    let fluxVerifie = false;

    boutonBascule.addEventListener('click', async () => {
      const estOuvert = panneau.classList.toggle('ouvert');
      boutonBascule.setAttribute('aria-expanded', estOuvert);
      boutonBascule.innerHTML = libelleBascule(estOuvert);
      if (estOuvert && !fluxVerifie && _fluxEnAttente) {
        fluxVerifie = true;
        await chargerQualites();
      }
    });
  }

  // ═════════════════════════════════════════════════════════════════════════
  // Lecteur à forme d'onde
  // ═════════════════════════════════════════════════════════════════════════
  const VITESSES_LECTURE = [1, 1.25, 1.5, 2, 0.75];
  const SAUT_SECONDES    = 10;
  const PAS_CLAVIER      = 5;
  const CLE_VOLUME       = 'sc_scraper_volume';
  const RVB_ROUGE        = '225, 29, 46';
  const borner = (valeur, min, max) => Math.min(max, Math.max(min, valeur));

  function formaterTemps(secondes) {
    if (!isFinite(secondes) || secondes < 0) secondes = 0;
    const total   = Math.floor(secondes);
    const heures  = Math.floor(total / 3600);
    const minutes = Math.floor((total % 3600) / 60);
    const reste   = String(total % 60).padStart(2, '0');
    return heures > 0 ? heures + ':' + String(minutes).padStart(2, '0') + ':' + reste : minutes + ':' + reste;
  }

  const CHEMIN_REPLAY = 'M12 5V1L7 6l5 5V7c3.31 0 6 2.69 6 6s-2.69 6-6 6-6-2.69-6-6H4c0 4.42 3.58 8 8 8s8-3.58 8-8-3.58-8-8-8z';
  const TEXTE_SAUT    = '<text x="12" y="16" text-anchor="middle" font-size="7.5" font-weight="700" font-family="system-ui, sans-serif">10</text>';
  const CORPS_HAUT_PARLEUR = '<path d="M4 9.5h3.2L12 5.5v13l-4.8-4H4z" fill="currentColor"/>';
  const ICONES_LECTEUR = {
    lecture: '<svg class="icone-lecture" width="26" height="26" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2" stroke-linejoin="round" aria-hidden="true"><path d="M8 5.5v13l11-6.5z"/></svg>',
    pause:   '<svg class="icone-pause" width="26" height="26" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><rect x="6.2" y="4.8" width="4.2" height="14.4" rx="1.6"/><rect x="13.6" y="4.8" width="4.2" height="14.4" rx="1.6"/></svg>',
    reculer: '<svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="' + CHEMIN_REPLAY + '"/>' + TEXTE_SAUT + '</svg>',
    avancer: '<svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><g transform="translate(24 0) scale(-1 1)"><path d="' + CHEMIN_REPLAY + '"/></g>' + TEXTE_SAUT + '</svg>',
    volHaut: '<svg class="vol-haut" width="20" height="20" viewBox="0 0 24 24" aria-hidden="true">' + CORPS_HAUT_PARLEUR + '<path d="M15.5 9.2a4.2 4.2 0 0 1 0 5.6M18.2 6.6a8 8 0 0 1 0 10.8" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>',
    volBas:  '<svg class="vol-bas" width="20" height="20" viewBox="0 0 24 24" aria-hidden="true">' + CORPS_HAUT_PARLEUR + '<path d="M15.5 9.2a4.2 4.2 0 0 1 0 5.6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>',
    volMuet: '<svg class="vol-muet" width="20" height="20" viewBox="0 0 24 24" aria-hidden="true">' + CORPS_HAUT_PARLEUR + '<path d="M16 9.5l5 5M21 9.5l-5 5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>',
  };

  function htmlLecteur() {
    return `<section class="lecteur" aria-label="Lecteur audio">
      <div class="lecteur-lecture">
        <button type="button" class="lecteur-bouton" data-role="lecture" aria-label="Lire">
          <span class="lecteur-bouton-halo" aria-hidden="true"></span>
          ${ICONES_LECTEUR.lecture}${ICONES_LECTEUR.pause}
          <span class="spinner spinner-blanc" aria-hidden="true"></span>
        </button>
      </div>
      <div class="lecteur-temps">
        <span class="lecteur-temps-courant" data-role="temps-courant">0:00</span>
        <span class="lecteur-temps-total">/ <span data-role="temps-total">0:00</span></span>
        <span class="egaliseur" aria-hidden="true"><i></i><i></i><i></i><i></i></span>
      </div>
      <div class="lecteur-outils">
        <button type="button" class="lecteur-outil" data-role="reculer" aria-label="Reculer de ${SAUT_SECONDES} secondes">${ICONES_LECTEUR.reculer}</button>
        <button type="button" class="lecteur-outil" data-role="avancer" aria-label="Avancer de ${SAUT_SECONDES} secondes">${ICONES_LECTEUR.avancer}</button>
        <button type="button" class="lecteur-outil lecteur-vitesse" data-role="vitesse" aria-label="Vitesse de lecture : 1">1×</button>
        <div class="lecteur-volume" data-role="zone-volume" data-niveau="haut">
          <button type="button" class="lecteur-outil" data-role="muet" aria-label="Couper le son">${ICONES_LECTEUR.volHaut}${ICONES_LECTEUR.volBas}${ICONES_LECTEUR.volMuet}</button>
          <input type="range" class="lecteur-curseur" data-role="volume" min="0" max="100" step="1" value="100" aria-label="Volume" />
        </div>
      </div>
      <div class="lecteur-bas">
        <div class="lecteur-onde chargement" data-role="onde" tabindex="0" role="slider" aria-label="Position de lecture" aria-valuemin="0" aria-valuemax="0" aria-valuenow="0" aria-valuetext="0:00" aria-busy="true">
          <canvas></canvas>
          <div class="lecteur-infobulle" data-role="infobulle" hidden>0:00</div>
        </div>
        <p class="lecteur-message" data-role="message" role="status" hidden></p>
      </div>
    </section>`;
  }

  /** Flux lisibles directement : MP3 progressif d'abord, puis HLS non chiffré. */
  function classerFlux(flux) {
    return (flux || [])
      .map((s, index) => ({ index, url: s.url, protocole: (s.protocol || '').toLowerCase(), mime: (s.mime || '').toLowerCase() }))
      .filter(c => c.url && !c.protocole.includes('encrypted'))
      .map(c => ({
        ...c,
        rang: c.protocole === 'progressive' ? 0
            : c.protocole === 'hls' ? (c.mime.includes('mpeg') ? 1 : c.mime.includes('mp4') ? 2 : 9)
            : 9,
      }))
      .filter(c => c.rang < 9)
      .sort((a, b) => a.rang - b.rang);
  }

  /** Onde de substitution, stable pour un même titre, affichée si SoundCloud ne fournit pas la vraie. */
  function genererOndeFictive(graine, nombre = 400) {
    const aleatoire = generateurAleatoire(graine);
    const phases = [aleatoire() * 6.28, aleatoire() * 6.28];
    const echantillons = [];
    for (let i = 0; i < nombre; i++) {
      const t = i / nombre;
      const enveloppe = 0.45 + 0.35 * Math.abs(Math.sin(t * 5.1 + phases[0])) + 0.2 * Math.abs(Math.sin(t * 13 + phases[1]));
      const attaque = Math.min(1, t * 30);
      const fin = 0.6 + 0.4 * Math.min(1, (1 - t) * 12);
      echantillons.push(borner(enveloppe * (0.55 + 0.45 * aleatoire()) * attaque * fin, 0.05, 1));
    }
    return echantillons;
  }

  function rectArrondi(chemin, x, y, largeur, hauteur, rayon) {
    if (chemin.roundRect) chemin.roundRect(x, y, largeur, hauteur, rayon);
    else chemin.rect(x, y, largeur, hauteur);
  }

  let _promesseHls = null;
  function chargerBibliothequeHls() {
    if (window.Hls) return Promise.resolve(window.Hls);
    if (!_promesseHls) {
      _promesseHls = new Promise((resoudre, rejeter) => {
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/hls.js@1/dist/hls.min.js';
        script.onload  = () => resoudre(window.Hls);
        script.onerror = () => { _promesseHls = null; rejeter(new Error('hls.js indisponible')); };
        document.head.appendChild(script);
      });
    }
    return _promesseHls;
  }

  class LecteurOnde {
    constructor(racine, { piste, artiste, idClient, authPiste }) {
      this.racine     = racine;
      this.piste      = piste;
      this.artiste    = artiste;
      this.idClient   = idClient;
      this.authPiste  = authPiste || '';
      this.dureePiste = (Number(piste.duration_ms) || 0) / 1000;
      this.candidats  = classerFlux(piste.audio_streams);
      this.controleur = new AbortController();
      this.mouvementReduit = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

      const el = nom => racine.querySelector('[data-role="' + nom + '"]');
      this.boutonLecture  = el('lecture');
      this.tempsCourant   = el('temps-courant');
      this.tempsTotal     = el('temps-total');
      this.boutonReculer  = el('reculer');
      this.boutonAvancer  = el('avancer');
      this.boutonVitesse  = el('vitesse');
      this.boutonMuet     = el('muet');
      this.zoneVolume     = el('zone-volume');
      this.curseurVolume  = el('volume');
      this.zoneOnde       = el('onde');
      this.infobulle      = el('infobulle');
      this.zoneMessage    = el('message');
      this.canvas         = this.zoneOnde.querySelector('canvas');
      this.ctx            = this.canvas.getContext('2d');

      // Audio
      this.audio = new Audio();
      this.audio.preload = 'metadata';
      try {
        const volumeEnregistre = parseFloat(localStorage.getItem(CLE_VOLUME));
        if (isFinite(volumeEnregistre)) this.audio.volume = borner(volumeEnregistre, 0, 1);
      } catch (_) { /* stockage indisponible : volume par défaut */ }

      // État de lecture
      this.sourcePrete       = false;
      this.promesseSource    = null;
      this.hls               = null;
      this.lectureDemandee   = false;
      this.enAttente         = false;
      this.termine           = false;
      this.positionEnAttente = null;
      this.extraitSignale    = false;
      this.detruit           = false;

      // État de l'onde
      this.glisse         = false;
      this.positionGlisse = 0;
      this.survol         = false;
      this.xSurvol        = 0;
      this.ratioMemoire   = 0;
      this.secondeAffichee = -1;
      this.imageDemandee  = 0;
      this.largeur = 0; this.hauteur = 0; this.dpr = 1;
      this.nbBarres = 0; this.pas = 5; this.largeurBarre = 3;
      this.barres    = new Float32Array(0);
      this.affichage = new Float32Array(0);
      this.bosse     = new Float32Array(0);
      this.echantillons = genererOndeFictive(Number(piste.id) % 100000 || 2718);
      this.debutIntro   = performance.now();

      const style = getComputedStyle(racine);
      const lire = (nom, secours) => style.getPropertyValue(nom).trim() || secours;
      this.couleurs = {
        reste:      lire('--onde-reste', '#e1e1e9'),
        memoire:    lire('--onde-memoire', '#cdcdd8'),
        rouge:      lire('--rouge', '#e11d2e'),
        rougeClair: lire('--rouge-clair', '#f4606c'),
      };

      this.lierEvenements();
      this.configurerSessionMedia();
      this.observateur = new ResizeObserver(() => this.redimensionner());
      this.observateur.observe(this.zoneOnde);

      this.majTempsTotal();
      this.majVolume();
      this.majVitesse();
      this.majEtat();
      this.chargerFormeOnde();
      this.preparerSource().catch(() => { /* le message d'erreur est déjà affiché */ });
    }

    get enLecture() { return !this.audio.paused && !this.audio.ended; }

    /** Durée de référence : celle du flux si elle colle à celle du titre, sinon celle du titre (extrait). */
    get duree() {
      const flux = this.audio.duration, titre = this.dureePiste;
      if (isFinite(flux) && flux > 0) {
        if (!titre || Math.abs(flux - titre) / titre < 0.03 || flux > titre) return flux;
        return titre;
      }
      return titre || 0;
    }

    positionCourante() {
      return this.sourcePrete ? this.audio.currentTime : (this.positionEnAttente || 0);
    }

    ratioAffiche() {
      if (this.glisse) return this.positionGlisse;
      const duree = this.duree;
      if (!duree) return 0;
      if (this.termine) return 1;
      return borner(this.positionCourante() / duree, 0, 1);
    }

    // ── Source audio ─────────────────────────────────────────────────────
    async resoudreUrl(candidat) {
      const donnees = [{ index: candidat.index, url: candidat.url }];
      const reponse = await fetch(
        './sc.php?action=check_streams'
        + '&client_id='           + encodeURIComponent(this.idClient)
        + '&track_authorization=' + encodeURIComponent(this.authPiste)
        + '&streams='             + encodeURIComponent(JSON.stringify(donnees)),
        { signal: this.controleur.signal }
      );
      const resultat = await reponse.json();
      return (resultat.success && resultat.valid.length) ? resultat.valid[0].url : null;
    }

    async essayerSource(candidat) {
      const url = await this.resoudreUrl(candidat);
      if (!url) throw new Error('Flux indisponible');
      if (this.detruit) throw new Error('Lecteur fermé');

      const audio = this.audio;
      if (this.hls) { this.hls.destroy(); this.hls = null; }

      if (candidat.protocole === 'hls' && !audio.canPlayType('application/vnd.apple.mpegurl')) {
        const Hls = await chargerBibliothequeHls();
        if (!Hls || !Hls.isSupported()) throw new Error('HLS non pris en charge');
        await new Promise((resoudre, rejeter) => {
          let pret = false;
          const hls = new Hls({ enableWorker: true });
          this.hls = hls;
          hls.on(Hls.Events.MANIFEST_PARSED, () => { pret = true; resoudre(); });
          hls.on(Hls.Events.ERROR, (_, erreur) => {
            if (!erreur.fatal) return;
            if (pret) this.afficherMessage('Le flux audio a été interrompu. Relancez la lecture.');
            else rejeter(new Error('Flux HLS illisible'));
          });
          hls.loadSource(url);
          hls.attachMedia(audio);
        });
      } else {
        await new Promise((resoudre, rejeter) => {
          const delai = setTimeout(echec, 12000);
          function nettoyer() {
            clearTimeout(delai);
            audio.removeEventListener('loadedmetadata', succes);
            audio.removeEventListener('error', echec);
          }
          function succes() { nettoyer(); resoudre(); }
          function echec()  { nettoyer(); rejeter(new Error('Flux illisible')); }
          audio.addEventListener('loadedmetadata', succes);
          audio.addEventListener('error', echec);
          audio.src = url;
          audio.load();
        });
      }
    }

    async chargerSource() {
      let derniereErreur = null;
      for (const candidat of this.candidats) {
        try {
          await this.essayerSource(candidat);
          this.sourcePrete = true;
          if (this.positionEnAttente !== null) {
            this.audio.currentTime = this.positionEnAttente;
            this.positionEnAttente = null;
          }
          this.majTempsTotal();
          this.verifierExtrait();
          this.majMemoire();
          this.demanderImage();
          return;
        } catch (e) {
          derniereErreur = e;
          if (this.detruit) throw e;
        }
      }
      throw derniereErreur || new Error('Aucun flux lisible');
    }

    preparerSource() {
      if (this.sourcePrete) return Promise.resolve();
      if (!this.promesseSource) {
        this.promesseSource = this.chargerSource().catch(e => {
          this.promesseSource = null;
          if (!this.detruit) this.afficherMessage('Lecture impossible pour ce titre : le flux audio est indisponible.');
          throw e;
        });
      }
      return this.promesseSource;
    }

    verifierExtrait() {
      const flux = this.audio.duration;
      if (this.extraitSignale || !isFinite(flux) || flux <= 0 || !this.dureePiste) return;
      if (flux < this.dureePiste * 0.97) {
        this.extraitSignale = true;
        this.afficherMessage('Seul un extrait de ' + formaterTemps(flux) + ' est diffusé pour ce titre.', false);
      }
    }

    // ── Commandes ────────────────────────────────────────────────────────
    basculerLecture() {
      if (this.enLecture || this.lectureDemandee) this.mettrePause();
      else this.lire();
    }

    async lire() {
      if (this.detruit) return;
      this.lectureDemandee = true;
      if (this.messageEstErreur) this.afficherMessage('');
      this.majEtat();
      try {
        await this.preparerSource();
      } catch (e) {
        this.lectureDemandee = false;
        this.majEtat();
        return;
      }
      if (!this.lectureDemandee || this.detruit) return;
      try {
        await this.audio.play();
      } catch (e) {
        this.lectureDemandee = false;
        if (e && e.name !== 'AbortError') this.afficherMessage('Le navigateur a bloqué la lecture. Appuyez de nouveau sur lecture.');
      }
      this.majEtat();
    }

    mettrePause() {
      this.lectureDemandee = false;
      if (!this.audio.paused) this.audio.pause();
      this.majEtat();
    }

    aller(secondes) {
      const duree = this.duree;
      const limite = (isFinite(this.audio.duration) && this.audio.duration > 0) ? this.audio.duration : duree;
      const cible = borner(secondes, 0, Math.max(0, limite - 0.1));
      this.termine = false;
      if (this.sourcePrete) this.audio.currentTime = cible;
      else this.positionEnAttente = cible;
      this.majTemps();
      this.demanderImage();
    }

    decaler(delta) { this.aller(this.positionCourante() + delta); }

    changerVitesse() {
      const indice = VITESSES_LECTURE.indexOf(this.audio.playbackRate);
      this.audio.playbackRate = VITESSES_LECTURE[(indice + 1) % VITESSES_LECTURE.length];
    }

    changerVolume(valeur) {
      this.audio.muted = false;
      this.audio.volume = borner(valeur, 0, 1);
      try { localStorage.setItem(CLE_VOLUME, String(this.audio.volume)); } catch (_) { /* facultatif */ }
    }

    basculerMuet() {
      const audio = this.audio;
      if (audio.muted || audio.volume === 0) {
        audio.muted = false;
        if (audio.volume === 0) audio.volume = 0.6;
      } else {
        audio.muted = true;
      }
    }

    // ── Mises à jour de l'interface ──────────────────────────────────────
    majEtat() {
      const enLecture = this.enLecture;
      const attente = (this.lectureDemandee && !enLecture) || (enLecture && this.enAttente);
      this.racine.classList.toggle('en-lecture', enLecture);
      this.racine.classList.toggle('attente', attente);
      this.boutonLecture.setAttribute('aria-label', (enLecture || this.lectureDemandee) ? 'Mettre en pause' : 'Lire');
    }

    majTemps() {
      const duree = this.duree;
      const temps = this.glisse ? this.positionGlisse * duree : this.positionCourante();
      const seconde = Math.floor(temps);
      if (seconde === this.secondeAffichee) return;
      this.secondeAffichee = seconde;
      this.tempsCourant.textContent = formaterTemps(temps);
      this.zoneOnde.setAttribute('aria-valuenow', seconde);
      this.zoneOnde.setAttribute('aria-valuetext', formaterTemps(temps) + ' sur ' + formaterTemps(duree));
    }

    majTempsTotal() {
      const duree = this.duree;
      this.tempsTotal.textContent = formaterTemps(duree);
      this.zoneOnde.setAttribute('aria-valuemax', Math.floor(duree));
      this.secondeAffichee = -1;
      this.majTemps();
    }

    majMemoire() {
      const plages = this.audio.buffered, duree = this.duree;
      if (!duree || !plages.length) return;
      const t = this.audio.currentTime;
      let fin = plages.end(plages.length - 1);
      for (let i = 0; i < plages.length; i++) {
        if (plages.start(i) <= t + 0.5 && plages.end(i) >= t) { fin = plages.end(i); break; }
      }
      this.ratioMemoire = borner(fin / duree, 0, 1);
    }

    majVolume() {
      const audio = this.audio;
      const pourcent = audio.muted ? 0 : Math.round(audio.volume * 100);
      const niveau = (audio.muted || audio.volume === 0) ? 'muet' : audio.volume < 0.5 ? 'bas' : 'haut';
      this.zoneVolume.dataset.niveau = niveau;
      this.curseurVolume.value = pourcent;
      this.curseurVolume.style.setProperty('--p', pourcent + '%');
      this.boutonMuet.setAttribute('aria-label', niveau === 'muet' ? 'Réactiver le son' : 'Couper le son');
    }

    majVitesse() {
      const vitesse = this.audio.playbackRate;
      const texte = String(vitesse).replace('.', ',');
      this.boutonVitesse.textContent = texte + '×';
      this.boutonVitesse.setAttribute('aria-label', 'Vitesse de lecture : ' + texte);
    }

    afficherMessage(texte, erreur = true) {
      this.messageEstErreur = !!texte && erreur;
      this.zoneMessage.textContent = texte;
      this.zoneMessage.classList.toggle('erreur-lecture', this.messageEstErreur);
      this.zoneMessage.hidden = !texte;
    }

    // ── Forme d'onde ─────────────────────────────────────────────────────
    async chargerFormeOnde() {
      try {
        if (!this.piste.waveform_url) throw new Error('Pas de forme d’onde');
        const reponse = await fetch('./sc.php?action=forme_onde&url=' + encodeURIComponent(this.piste.waveform_url),
          { signal: this.controleur.signal });
        const resultat = await reponse.json();
        if (!resultat.success || !Array.isArray(resultat.echantillons) || resultat.echantillons.length < 10) {
          throw new Error('Forme d’onde indisponible');
        }
        this.echantillons = resultat.echantillons.map(Number);
        this.recalculerBarres(false);
      } catch (e) {
        if (e.name === 'AbortError') return;
        /* on garde l'onde de substitution */
      }
      if (this.detruit) return;
      this.zoneOnde.classList.remove('chargement');
      this.zoneOnde.removeAttribute('aria-busy');
      this.demanderImage();
    }

    recalculerBarres(immediat) {
      const nb = this.nbBarres;
      if (!nb) return;
      const source = this.echantillons, total = source.length;
      const barres = new Float32Array(nb);
      let plafond = 0;
      for (let i = 0; i < nb; i++) {
        const debut = Math.floor(i * total / nb);
        const fin   = Math.max(debut + 1, Math.floor((i + 1) * total / nb));
        let max = 0, somme = 0;
        for (let j = debut; j < fin; j++) { const v = source[j]; if (v > max) max = v; somme += v; }
        barres[i] = 0.55 * max + 0.45 * somme / (fin - debut);
        if (barres[i] > plafond) plafond = barres[i];
      }
      for (let i = 0; i < nb; i++) barres[i] = Math.max(0.06, Math.pow(barres[i] / (plafond || 1), 0.85));
      this.barres = barres;
      if (immediat || this.affichage.length !== nb) {
        this.affichage = Float32Array.from(barres);
        this.bosse = new Float32Array(nb);
      }
      this.demanderImage();
    }

    redimensionner() {
      if (this.detruit) return;
      const rect = this.zoneOnde.getBoundingClientRect();
      const largeur = Math.floor(rect.width), hauteur = Math.floor(rect.height);
      if (!largeur || !hauteur) return;
      const dpr = Math.min(window.devicePixelRatio || 1, 3);
      if (largeur === this.largeur && hauteur === this.hauteur && dpr === this.dpr) return;

      this.largeur = largeur; this.hauteur = hauteur; this.dpr = dpr;
      this.canvas.width  = Math.round(largeur * dpr);
      this.canvas.height = Math.round(hauteur * dpr);

      const nb = Math.max(20, Math.floor(largeur / (largeur < 480 ? 4 : 5)));
      this.nbBarres     = nb;
      this.pas          = largeur / nb;
      this.largeurBarre = this.pas * 0.6;

      const degrade = this.ctx.createLinearGradient(0, 0, 0, hauteur);
      degrade.addColorStop(0, this.couleurs.rougeClair);
      degrade.addColorStop(0.5, this.couleurs.rouge);
      degrade.addColorStop(1, this.couleurs.rougeClair);
      this.degradeLu = degrade;

      this.recalculerBarres(false);
      this.dessiner(performance.now());
    }

    apparition(indice, maintenant) {
      if (this.mouvementReduit) return 1;
      const x = (maintenant - this.debutIntro - indice * 5) / 550;
      if (x >= 1) return 1;
      if (x <= 0) return 0;
      return 1 - Math.pow(1 - x, 3);
    }

    introActive(maintenant) {
      return !this.mouvementReduit && (maintenant - this.debutIntro) < this.nbBarres * 5 + 550;
    }

    demanderImage() {
      if (this.detruit || this.imageDemandee) return;
      this.imageDemandee = requestAnimationFrame(maintenant => {
        this.imageDemandee = 0;
        this.animer(maintenant);
      });
    }

    animer(maintenant) {
      const continuer = this.mettreAJourAnimation(maintenant);
      if (this.enLecture) this.majTemps();
      this.dessiner(maintenant);
      if (continuer) this.demanderImage();
    }

    /** Fait évoluer la bosse sous le curseur et le fondu entre deux formes d'onde. Renvoie vrai tant qu'il reste à animer. */
    mettreAJourAnimation(maintenant) {
      const nb = this.nbBarres;
      if (!nb) return false;
      const enLecture = this.enLecture;
      const xLecture = this.ratioAffiche() * this.largeur;
      let stable = true;
      for (let i = 0; i < nb; i++) {
        const centre = (i + 0.5) * this.pas;
        let cibleBosse = 0;
        if (this.survol) { const d = (centre - this.xSurvol) / 70; cibleBosse += 0.34 * Math.exp(-d * d); }
        if (enLecture)   { const d = (centre - xLecture) / 38;     cibleBosse += 0.14 * Math.exp(-d * d); }
        const ecartBosse   = cibleBosse - this.bosse[i];
        const ecartHauteur = this.barres[i] - this.affichage[i];
        this.bosse[i]     = this.mouvementReduit ? cibleBosse : this.bosse[i] + ecartBosse * 0.22;
        this.affichage[i] = this.mouvementReduit ? this.barres[i] : this.affichage[i] + ecartHauteur * 0.16;
        if (Math.abs(ecartBosse) > 0.004 || Math.abs(ecartHauteur) > 0.004) stable = false;
      }
      return !stable || enLecture || this.glisse || this.introActive(maintenant);
    }

    dessiner(maintenant) {
      const { ctx, largeur, hauteur, nbBarres: nb } = this;
      if (!largeur || !nb) return;
      ctx.setTransform(this.dpr, 0, 0, this.dpr, 0, 0);
      ctx.clearRect(0, 0, largeur, hauteur);

      const ratio    = this.ratioAffiche();
      const xLecture = ratio * largeur;
      const xSurvol  = this.survol ? this.xSurvol : -1;
      const xMemoire = this.ratioMemoire * largeur;
      const centreY  = hauteur / 2;

      // Lueur autour de la tête de lecture
      // (ellipse : le dégradé s'éteint exactement en haut et en bas, sans bord net)
      if (xLecture > 0) {
        const rayonX = Math.min(170, largeur * 0.4), rayonY = hauteur / 2;
        const intensite = (this.enLecture || this.glisse) ? 0.16 : 0.07;
        ctx.save();
        ctx.translate(xLecture, centreY);
        ctx.scale(rayonX / rayonY, 1);
        const lueur = ctx.createRadialGradient(0, 0, 0, 0, 0, rayonY);
        lueur.addColorStop(0,   'rgba(' + RVB_ROUGE + ', ' + intensite + ')');
        lueur.addColorStop(0.55, 'rgba(' + RVB_ROUGE + ', ' + (intensite * 0.35).toFixed(3) + ')');
        lueur.addColorStop(1,   'rgba(' + RVB_ROUGE + ', 0)');
        ctx.fillStyle = lueur;
        ctx.fillRect(-rayonY, -centreY, rayonY * 2, hauteur);
        ctx.restore();
      }

      // Barres, regroupées par couleur pour limiter les appels de remplissage
      const chemins = { lu: new Path2D(), apercu: new Path2D(), memoire: new Path2D(), reste: new Path2D() };
      const hauteurMax = hauteur * 0.94;
      for (let i = 0; i < nb; i++) {
        const apparition = this.apparition(i, maintenant);
        const h  = Math.min(hauteurMax, this.affichage[i] * (1 + this.bosse[i]) * hauteurMax) * apparition;
        const hh = Math.max(4 * apparition, h);
        if (hh < 0.5) continue;
        const x = i * this.pas + (this.pas - this.largeurBarre) / 2;
        const centre = x + this.largeurBarre / 2;
        const chemin = centre <= xLecture ? chemins.lu
                     : centre <= xSurvol  ? chemins.apercu
                     : centre <= xMemoire ? chemins.memoire
                     : chemins.reste;
        rectArrondi(chemin, x, centreY - hh / 2, this.largeurBarre, hh, this.largeurBarre / 2);
      }
      ctx.fillStyle = this.couleurs.reste;   ctx.fill(chemins.reste);
      ctx.fillStyle = this.couleurs.memoire; ctx.fill(chemins.memoire);
      ctx.fillStyle = 'rgba(' + RVB_ROUGE + ', .34)'; ctx.fill(chemins.apercu);
      ctx.fillStyle = this.degradeLu;        ctx.fill(chemins.lu);

      // Repère de survol
      if (xSurvol >= 0 && !this.glisse) {
        ctx.fillStyle = 'rgba(' + RVB_ROUGE + ', .5)';
        ctx.fillRect(Math.round(xSurvol) - 0.5, hauteur * 0.08, 1, hauteur * 0.84);
      }

      // Tête de lecture
      if ((ratio > 0.001 && ratio < 0.999) || this.glisse) {
        ctx.fillStyle = this.couleurs.rouge;
        ctx.fillRect(xLecture - 1, hauteur * 0.04, 2, hauteur * 0.92);
      }
      if (this.survol || this.glisse) {
        ctx.save();
        ctx.shadowColor = 'rgba(28, 28, 33, .3)';
        ctx.shadowBlur = 6;
        ctx.shadowOffsetY = 1;
        ctx.beginPath();
        ctx.arc(xLecture, centreY, 7, 0, Math.PI * 2);
        ctx.fillStyle = '#fff';
        ctx.fill();
        ctx.restore();
        ctx.beginPath();
        ctx.arc(xLecture, centreY, 7, 0, Math.PI * 2);
        ctx.lineWidth = 3;
        ctx.strokeStyle = this.couleurs.rouge;
        ctx.stroke();
      }
    }

    // ── Pointeur et clavier sur l'onde ───────────────────────────────────
    ratioDepuis(evenement) {
      const rect = this.zoneOnde.getBoundingClientRect();
      return borner((evenement.clientX - rect.left) / rect.width, 0, 1);
    }

    survoler(evenement) {
      const ratio = this.ratioDepuis(evenement);
      this.survol  = true;
      this.xSurvol = ratio * this.largeur;
      this.infobulle.textContent = formaterTemps(ratio * this.duree);
      this.infobulle.hidden = false;
      const moitie = this.infobulle.offsetWidth / 2;
      this.infobulle.style.left = borner(this.xSurvol, moitie, this.largeur - moitie) + 'px';
      this.demanderImage();
    }

    quitterSurvol() {
      this.survol = false;
      this.infobulle.hidden = true;
      this.demanderImage();
    }

    lierEvenements() {
      const audio = this.audio, onde = this.zoneOnde;

      this.boutonLecture.addEventListener('click', () => this.basculerLecture());
      this.boutonReculer.addEventListener('click', () => this.decaler(-SAUT_SECONDES));
      this.boutonAvancer.addEventListener('click', () => this.decaler(SAUT_SECONDES));
      this.boutonVitesse.addEventListener('click', () => this.changerVitesse());
      this.boutonMuet.addEventListener('click', () => this.basculerMuet());
      this.curseurVolume.addEventListener('input', () => this.changerVolume(Number(this.curseurVolume.value) / 100));

      onde.addEventListener('pointerenter', e => { if (e.pointerType === 'mouse') this.survoler(e); });
      onde.addEventListener('pointermove', e => {
        if (this.glisse) {
          this.positionGlisse = this.ratioDepuis(e);
          this.survoler(e);
          this.majTemps();
        } else if (e.pointerType === 'mouse') {
          this.survoler(e);
        }
      });
      onde.addEventListener('pointerleave', () => { if (!this.glisse) this.quitterSurvol(); });
      onde.addEventListener('pointerdown', e => {
        if (e.button !== undefined && e.button !== 0) return;
        try { onde.setPointerCapture(e.pointerId); } catch (_) { /* capture facultative */ }
        onde.focus({ preventScroll: true });
        this.glisse = true;
        this.positionGlisse = this.ratioDepuis(e);
        this.survoler(e);
        this.majTemps();
      });
      const relacher = e => {
        if (!this.glisse) return;
        this.glisse = false;
        if (e.type === 'pointerup' && this.duree > 0) this.aller(this.positionGlisse * this.duree);
        if (e.pointerType !== 'mouse' || !onde.matches(':hover')) this.quitterSurvol();
        this.secondeAffichee = -1;
        this.majTemps();
        this.demanderImage();
      };
      onde.addEventListener('pointerup', relacher);
      onde.addEventListener('pointercancel', relacher);

      onde.addEventListener('keydown', e => {
        const pas = e.shiftKey ? PAS_CLAVIER * 3 : PAS_CLAVIER;
        switch (e.key) {
          case 'ArrowLeft': case 'ArrowDown': this.decaler(-pas); break;
          case 'ArrowRight': case 'ArrowUp':  this.decaler(pas); break;
          case 'PageDown': this.decaler(-this.duree * 0.1); break;
          case 'PageUp':   this.decaler(this.duree * 0.1); break;
          case 'Home':     this.aller(0); break;
          case 'End':      this.aller(this.duree); break;
          case ' ': case 'k': case 'K': this.basculerLecture(); break;
          case 'm': case 'M': this.basculerMuet(); break;
          default: return;
        }
        e.preventDefault();
      });

      audio.addEventListener('play',    () => { this.termine = false; this.majEtat(); this.demanderImage(); });
      audio.addEventListener('playing', () => { this.enAttente = false; this.majEtat(); this.demanderImage(); });
      audio.addEventListener('pause',   () => { this.lectureDemandee = false; this.majEtat(); this.majTemps(); this.demanderImage(); });
      audio.addEventListener('ended',   () => { this.termine = true; this.lectureDemandee = false; this.majEtat(); this.majTemps(); this.demanderImage(); });
      audio.addEventListener('waiting', () => { this.enAttente = true; this.majEtat(); });
      audio.addEventListener('canplay', () => { this.enAttente = false; this.majEtat(); });
      audio.addEventListener('seeked',  () => { this.termine = false; this.majTemps(); this.majMemoire(); this.demanderImage(); });
      audio.addEventListener('timeupdate', () => { this.majTemps(); this.majMemoire(); this.demanderImage(); });
      audio.addEventListener('progress',   () => { this.majMemoire(); this.demanderImage(); });
      audio.addEventListener('durationchange', () => { this.majTempsTotal(); this.verifierExtrait(); this.demanderImage(); });
      audio.addEventListener('volumechange',   () => this.majVolume());
      audio.addEventListener('ratechange',     () => this.majVitesse());
      audio.addEventListener('error', () => {
        if (this.detruit || !this.sourcePrete) return;
        this.lectureDemandee = false;
        this.majEtat();
        this.afficherMessage('Le flux audio n’a pas pu être lu. Réessayez dans un instant.');
      });
    }

    // ── Commandes système (écran verrouillé, touches multimédia) ─────────
    configurerSessionMedia() {
      if (!('mediaSession' in navigator)) return;
      const nomArtiste = this.artiste && (this.artiste.full_name || this.artiste.username) || '';
      const pochette = this.piste.artwork_url
        ? [{ src: this.piste.artwork_url.replace('-large', '-t500x500'), sizes: '500x500' }]
        : [];
      try {
        navigator.mediaSession.metadata = new MediaMetadata({ title: this.piste.title || '', artist: nomArtiste, artwork: pochette });
      } catch (_) { /* métadonnées facultatives */ }
      const actions = {
        play:         () => this.lire(),
        pause:        () => this.mettrePause(),
        seekbackward: () => this.decaler(-SAUT_SECONDES),
        seekforward:  () => this.decaler(SAUT_SECONDES),
        seekto:       d => { if (typeof d.seekTime === 'number') this.aller(d.seekTime); },
      };
      for (const [nom, action] of Object.entries(actions)) {
        try { navigator.mediaSession.setActionHandler(nom, action); } catch (_) { /* action non prise en charge */ }
      }
    }

    detruire() {
      this.detruit = true;
      this.controleur.abort();
      cancelAnimationFrame(this.imageDemandee);
      this.observateur.disconnect();
      try { this.audio.pause(); } catch (_) { /* déjà arrêté */ }
      if (this.hls) { this.hls.destroy(); this.hls = null; }
      this.audio.removeAttribute('src');
      this.audio.load();
      if ('mediaSession' in navigator) {
        for (const nom of ['play', 'pause', 'seekbackward', 'seekforward', 'seekto']) {
          try { navigator.mediaSession.setActionHandler(nom, null); } catch (_) { /* ignoré */ }
        }
        try { navigator.mediaSession.metadata = null; } catch (_) { /* ignoré */ }
      }
    }
  }

  let _lecteurActif = null;

  function detruireLecteur() {
    if (!_lecteurActif) return;
    _lecteurActif.detruire();
    _lecteurActif = null;
  }

  function initialiserLecteur(d) {
    detruireLecteur();
    const racine = document.querySelector('#resultats .lecteur');
    if (!racine || !d.track || !d.api_client) return;
    fermerDock();   // un seul son à la fois
    _lecteurActif = new LecteurOnde(racine, {
      piste:     d.track,
      artiste:   d.artist,
      idClient:  d.api_client.id,
      authPiste: d.track.track_authorization,
    });
  }

  // ═════════════════════════════════════════════════════════════════════════
  // Lecteur ancré en bas de page : lecture directe depuis les cartes
  // ═════════════════════════════════════════════════════════════════════════
  let _lecteurDock          = null;   // instance de LecteurOnde du lecteur ancré
  let _pisteDock            = null;   // { url } du titre chargé dans le lecteur ancré
  let _chargementDock       = null;   // url du titre en cours de préparation
  let _compteurDock         = 0;      // si plusieurs titres sont demandés, le dernier clic gagne
  let _minuteurNotification = null;

  function afficherNotification(texte) {
    const zone = parId('notification');
    zone.innerHTML = icone('alerte', 18) + '<span>' + echapper(texte) + '</span>';
    zone.hidden = false;
    clearTimeout(_minuteurNotification);
    _minuteurNotification = setTimeout(() => { zone.hidden = true; }, 4500);
  }

  const nettoyerUrlPiste = url => {
    try { const u = new URL(url); return u.origin + u.pathname; } catch (_) { return url; }
  };

  function htmlDock({ titre, artiste, pochette }) {
    const image = pochette
      ? `<img class="dock-pochette" src="${echapper(pochette)}" alt="" />`
      : `<span class="dock-pochette dock-pochette-vide">${icone('note', 22)}</span>`;
    return `
      <div class="dock-info">
        ${image}
        <div class="dock-texte">
          <p class="dock-titre" title="${echapper(titre)}">${echapper(titre)}</p>
          <p class="dock-artiste">${echapper(artiste)}</p>
        </div>
      </div>
      <div class="dock-transport">
        <button type="button" class="lecteur-outil" data-role="reculer" aria-label="Reculer de ${SAUT_SECONDES} secondes">${ICONES_LECTEUR.reculer}</button>
        <button type="button" class="lecteur-bouton" data-role="lecture" aria-label="Lire">
          <span class="lecteur-bouton-halo" aria-hidden="true"></span>
          ${ICONES_LECTEUR.lecture}${ICONES_LECTEUR.pause}
          <span class="spinner spinner-blanc" aria-hidden="true"></span>
        </button>
        <button type="button" class="lecteur-outil" data-role="avancer" aria-label="Avancer de ${SAUT_SECONDES} secondes">${ICONES_LECTEUR.avancer}</button>
      </div>
      <div class="lecteur-temps">
        <span class="lecteur-temps-courant" data-role="temps-courant">0:00</span>
        <span class="lecteur-temps-total">/ <span data-role="temps-total">0:00</span></span>
        <span class="egaliseur" aria-hidden="true"><i></i><i></i><i></i><i></i></span>
      </div>
      <div class="lecteur-outils">
        <button type="button" class="lecteur-outil lecteur-vitesse" data-role="vitesse" aria-label="Vitesse de lecture : 1">1×</button>
        <div class="lecteur-volume" data-role="zone-volume" data-niveau="haut">
          <button type="button" class="lecteur-outil" data-role="muet" aria-label="Couper le son">${ICONES_LECTEUR.volHaut}${ICONES_LECTEUR.volBas}${ICONES_LECTEUR.volMuet}</button>
          <input type="range" class="lecteur-curseur" data-role="volume" min="0" max="100" step="1" value="100" aria-label="Volume" />
        </div>
      </div>
      <button type="button" class="lecteur-outil dock-fermer" data-role="fermer" aria-label="Fermer le lecteur">${icone('fermer', 18)}</button>
      <div class="lecteur-bas">
        <div class="lecteur-onde chargement" data-role="onde" tabindex="0" role="slider" aria-label="Position de lecture" aria-valuemin="0" aria-valuemax="0" aria-valuenow="0" aria-valuetext="0:00" aria-busy="true">
          <canvas></canvas>
          <div class="lecteur-infobulle" data-role="infobulle" hidden>0:00</div>
        </div>
        <p class="lecteur-message" data-role="message" role="status" hidden></p>
      </div>`;
  }

  /** Reflète l'état du lecteur ancré sur les cartes : titre en cours, lecture, chargement. */
  function synchroniserCartes() {
    const enLecture = !!(_lecteurDock && _lecteurDock.enLecture);
    document.querySelectorAll('.carte-piste').forEach(carte => {
      const courante = !!_pisteDock && _pisteDock.url === carte.dataset.url;
      const charge   = _chargementDock === carte.dataset.url;
      const joue     = courante && enLecture;
      const bouton   = carte.querySelector('.bouton-ecouter');
      const texte    = joue ? 'Pause' : courante ? 'Reprendre' : 'Écouter';
      carte.classList.toggle('en-ecoute', courante);
      carte.classList.toggle('en-lecture', joue);
      bouton.classList.toggle('charge', charge);
      bouton.disabled = charge;
      bouton.setAttribute('aria-pressed', joue);
      bouton.setAttribute('aria-label', texte + ' : ' + carte.querySelector('.carte-titre').textContent);
      bouton.querySelector('.bouton-libelle').innerHTML = icone(joue ? 'pause' : 'lire', 16) + texte;
    });
  }

  function detruireLecteurDock() {
    const lecteur = _lecteurDock;
    _lecteurDock = null;
    _pisteDock = null;
    if (lecteur) lecteur.detruire();
  }

  function fermerDock() {
    detruireLecteurDock();
    const dock = parId('dock-lecteur');
    dock.hidden = true;
    dock.innerHTML = '';
    dock.classList.remove('en-lecture', 'attente');
    document.body.classList.remove('avec-dock');
    synchroniserCartes();
  }

  function ouvrirDock(d, pisteRecherche) {
    detruireLecteurDock();
    detruireLecteur();   // lecteur de la fiche détaillée : un seul son à la fois

    const dock = parId('dock-lecteur');
    const pochette = d.track.artwork_url || pisteRecherche.pochette;
    dock.innerHTML = htmlDock({
      titre:    d.track.title || pisteRecherche.titre,
      artiste:  (d.artist && (d.artist.full_name || d.artist.username)) || pisteRecherche.artiste || '',
      pochette: pochette ? pochette.replace('-large', '-t200x200') : null,
    });
    dock.classList.remove('en-lecture', 'attente');
    dock.hidden = false;
    document.body.classList.add('avec-dock');

    _pisteDock   = { url: pisteRecherche.url };
    _lecteurDock = new LecteurOnde(dock, {
      piste:     d.track,
      artiste:   d.artist,
      idClient:  d.api_client.id,
      authPiste: d.track.track_authorization,
    });
    ['play', 'pause', 'ended', 'emptied'].forEach(nom => _lecteurDock.audio.addEventListener(nom, synchroniserCartes));
    dock.querySelector('[data-role="fermer"]').addEventListener('click', fermerDock);
    synchroniserCartes();
    _lecteurDock.lire();
  }

  async function ecouterPiste(indice) {
    const piste = _rechercheActive && _rechercheActive.pistes[indice];
    if (!piste) return;

    // Même titre déjà dans le lecteur : simple bascule lecture / pause
    if (_lecteurDock && _pisteDock && _pisteDock.url === piste.url) {
      _lecteurDock.basculerLecture();
      return;
    }
    if (_chargementDock === piste.url) return;

    const jeton = ++_compteurDock;
    _chargementDock = piste.url;
    synchroniserCartes();
    try {
      const reponse  = await fetch('./sc.php?url=' + encodeURIComponent(nettoyerUrlPiste(piste.url)));
      const resultat = await reponse.json();
      if (jeton !== _compteurDock) return;
      if (!resultat.success) throw new Error(resultat.error || 'Erreur inconnue du serveur');
      const d = resultat.data;
      if (!d.track || !d.api_client || !classerFlux(d.track.audio_streams).length) {
        throw new Error('Ce titre ne peut pas être écouté directement. Utilisez « Analyser ».');
      }
      ouvrirDock(d, piste);
    } catch (e) {
      if (jeton === _compteurDock) afficherNotification(e.message);
    } finally {
      if (jeton === _compteurDock) { _chargementDock = null; synchroniserCartes(); }
    }
  }

  // ── Rendu des résultats ──────────────────────────────────────────────────
  function htmlPiste(d) {
    const piste   = d.track;
    const artiste = d.artist;

    const pochette = piste.artwork_url
      ? `<img class="pochette" src="${echapper(piste.artwork_url.replace('-large', '-t500x500'))}" alt="Pochette de ${echapper(piste.title)}" />`
      : `<div class="pochette pochette-vide">${icone('note', 56)}</div>`;

    const nomArtiste = artiste && (artiste.full_name || artiste.username);
    const signature = nomArtiste
      ? `<p class="piste-artiste">${artiste.avatar_url ? `<img class="avatar avatar-mini" src="${echapper(artiste.avatar_url.replace('-large', '-t200x200'))}" alt="" />` : ''}<span>${echapper(nomArtiste)}</span></p>`
      : '';

    const puces = [piste.genre, piste.duration_human].filter(Boolean)
      .map(t => `<span class="puce">${echapper(t)}</span>`).join('');

    const peutTelecharger = piste.audio_streams && piste.audio_streams.length && d.api_client;
    if (peutTelecharger) {
      _fluxEnAttente = {
        flux:      piste.audio_streams,
        idClient:  d.api_client.id,
        authPiste: piste.track_authorization,
        titre:     piste.title || 'track',
      };
    }

    const boutonTelecharger = peutTelecharger
      ? `<button type="button" class="bouton bouton-primaire" id="bouton-bascule" aria-expanded="false" aria-controls="panneau-qualites">${libelleBascule(false)}</button>`
      : '';
    const lienSoundcloud = estUrlHttp(piste.permalink_url)
      ? `<a class="bouton bouton-secondaire" href="${echapper(piste.permalink_url)}" target="_blank" rel="noopener">Ouvrir sur SoundCloud ${icone('externe', 16)}</a>`
      : '';
    const lecteur = (peutTelecharger && classerFlux(piste.audio_streams).length) ? htmlLecteur() : '';
    const panneau = peutTelecharger
      ? `<div class="qualites" id="panneau-qualites"><div class="qualites-interieur"><div class="liste-qualites" id="liste-qualites">
           <div class="qualite-chargement" id="chargement-qualites"><span class="spinner"></span>Vérification des qualités disponibles…</div>
         </div></div></div>`
      : '';

    return `<section class="piste">
      <div>${pochette}</div>
      <div>
        ${signature}
        <h2 class="piste-titre">${echapper(piste.title)}</h2>
        <div class="puces">${puces}</div>
        <dl class="stats-piste">
          ${htmlStat('Écoutes', formater(piste.playback_count))}
          ${htmlStat('Likes', formater(piste.likes_count))}
          ${htmlStat('Reposts', formater(piste.reposts_count))}
          ${htmlStat('Commentaires', formater(piste.comment_count))}
          ${htmlStat('Téléchargements', formater(piste.download_count))}
        </dl>
        ${piste.description ? `<p class="piste-description">${echapper(couper(piste.description, 400))}</p>` : ''}
        <div class="piste-actions">${boutonTelecharger}${lienSoundcloud}</div>
        ${panneau}
      </div>
      ${lecteur}
      <dl class="details piste-details">
        ${ligneKv('Adresse', piste.permalink_url, true)}
        ${ligneKv('Mis en ligne le', piste.created_at && formaterDate(piste.created_at))}
        ${ligneKv('Date de sortie', piste.release_date && formaterDate(piste.release_date))}
        ${ligneKv('Label', piste.label_name)}
        ${ligneKv('Durée (ms)', piste.duration_ms)}
      </dl>
    </section>`;
  }

  function htmlArtiste(artiste) {
    const banniere = artiste.banner_url ? `<img class="banniere" src="${echapper(artiste.banner_url)}" alt="" />` : '';
    const avatar = artiste.avatar_url
      ? `<img class="avatar" src="${echapper(artiste.avatar_url.replace('-large', '-t200x200'))}" alt="" />`
      : `<span class="avatar"></span>`;
    const estVerifie = artiste.verified || (artiste.badges && artiste.badges.verified);

    const etiquettes = [];
    if (artiste.badges) {
      if (artiste.badges.pro)              etiquettes.push('Pro');
      if (artiste.badges.pro_unlimited)    etiquettes.push('Pro Unlimited');
      if (artiste.badges.creator_mid_tier) etiquettes.push('Créateur');
    }
    const htmlEtiquettes = etiquettes.length
      ? `<div class="puces">${etiquettes.map(b => `<span class="puce">${echapper(b)}</span>`).join('')}</div>`
      : '';

    return `<section class="rubrique">
      <h2 class="rubrique-titre">Artiste</h2>
      <div>
        ${banniere}
        <div class="artiste-entete">
          ${avatar}
          <div>
            <div class="artiste-nom">${echapper(artiste.full_name || artiste.username)}${estVerifie ? ICONE_VERIFIE : ''}</div>
            <div class="artiste-pseudo">@${echapper(artiste.username)}</div>
          </div>
          ${htmlEtiquettes}
        </div>
        <dl class="stats-artiste">
          ${htmlStat('Abonnés', formater(artiste.followers_count))}
          ${htmlStat('Abonnements', formater(artiste.followings_count))}
          ${htmlStat('Titres', formater(artiste.track_count))}
          ${htmlStat('Playlists', formater(artiste.playlist_count))}
          ${htmlStat('Likes', formater(artiste.likes_count))}
          ${htmlStat('Reposts', formater(artiste.reposts_count))}
          ${htmlStat('Commentaires', formater(artiste.comments_count))}
          ${htmlStat('Offre', echapper(artiste.subscription || '—'))}
        </dl>
        <dl class="details">
          ${ligneKv('Profil', artiste.permalink_url, true)}
          ${ligneKv('Permalink', artiste.permalink)}
          ${ligneKv('Ville', artiste.city)}
          ${ligneKv('Pays', artiste.country_code)}
          ${ligneKv('Description', artiste.description)}
        </dl>
      </div>
    </section>`;
  }

  function htmlEditeur(editeur) {
    return `<section class="rubrique">
      <h2 class="rubrique-titre">Éditeur</h2>
      <dl class="details">
        ${ligneKv('Artiste', editeur.artist)}
        ${ligneKv('Album', editeur.album_title)}
        ${ligneKv('Titre de la sortie', editeur.release_title)}
        ${ligneKv('ISRC', editeur.isrc, false, true)}
        ${ligneKv('UPC / EAN', editeur.upc_or_ean, false, true)}
        ${ligneKv('Ligne P', editeur.p_line)}
        ${ligneKv('Ligne C', editeur.c_line)}
      </dl>
    </section>`;
  }

  function afficherResultats(d) {
    const zone = parId('resultats');
    detruireLecteur();
    _fluxEnAttente = null;

    let html = '';
    if (d.track)                      html += htmlPiste(d);
    if (d.artist)                     html += htmlArtiste(d.artist);
    if (d.track && d.track.publisher) html += htmlEditeur(d.track.publisher);
    if (!html) html = '<div class="vide">Aucune donnée structurée trouvée pour cette adresse.</div>';
    if (_rechercheActive) {
      html = '<button type="button" class="bouton bouton-retour" id="bouton-retour">' + icone('gauche', 16) + 'Retour aux résultats</button>' + html;
    }

    zone.innerHTML = html;
    zone.hidden = false;
    parId('page').classList.add('a-des-resultats');
    initialiserTelechargement();
    initialiserLecteur(d);
  }

  // ── Recherche directe : liste de titres ──────────────────────────────────
  const TAILLE_PAGE = 12;
  let _rechercheActive = null;

  function htmlCartePiste(piste, indice) {
    const pochette = piste.pochette
      ? `<img src="${echapper(piste.pochette.replace('-large', '-t500x500'))}" alt="" loading="lazy" />`
      : `<span class="carte-pochette-vide">${icone('note', 40)}</span>`;
    const rondLecture = '<svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" style="margin-left:2px"><path d="M8 5.5v13l11-6.5z"/></svg>';
    const ecoutes = piste.ecoutes != null ? `<p class="carte-meta">${formater(piste.ecoutes)}\u00A0écoutes</p>` : '';
    return `<li class="carte-piste" data-indice="${indice}" data-url="${echapper(piste.url)}">
      <div class="carte-pochette">
        ${pochette}
        <span class="carte-lecture" aria-hidden="true"><span class="carte-lecture-rond">${rondLecture}<span class="carte-egaliseur"><i></i><i></i><i></i><i></i></span></span></span>
        ${piste.duree ? `<span class="carte-duree">${echapper(piste.duree)}</span>` : ''}
      </div>
      <div class="carte-texte">
        <h3 class="carte-titre">${echapper(piste.titre)}</h3>
        <p class="carte-artiste">${echapper(piste.artiste)}</p>
        ${ecoutes}
      </div>
      <div class="carte-actions">
        <button type="button" class="bouton bouton-primaire bouton-analyser" aria-label="Analyser ${echapper(piste.titre)}">Analyser</button>
        <button type="button" class="bouton bouton-contour bouton-ecouter" aria-pressed="false">
          <span class="spinner" aria-hidden="true"></span>
          <span class="bouton-libelle">${icone('lire', 16)}Écouter</span>
        </button>
      </div>
    </li>`;
  }

  async function recupererPistes(requete, decalage) {
    const reponse = await fetch('./sc.php?action=rechercher&q=' + encodeURIComponent(requete)
      + '&limit=' + TAILLE_PAGE + '&offset=' + decalage);
    const resultat = await reponse.json();
    if (!resultat.success) throw new Error(resultat.error || 'Erreur inconnue du serveur');
    return resultat;
  }

  function afficherResultatsRecherche() {
    const zone = parId('resultats-recherche');
    const { requete, pistes, suivant } = _rechercheActive;
    zone.innerHTML = pistes.length
      ? `<div>
           <h2 class="rubrique-titre">Résultats pour «\u00A0${echapper(requete)}\u00A0»</h2>
           <ul class="liste-pistes" id="liste-pistes">${pistes.map(htmlCartePiste).join('')}</ul>
           ${suivant ? '<div class="recherche-pied"><button type="button" class="bouton bouton-secondaire" id="bouton-plus">Afficher plus de titres</button></div>' : ''}
         </div>`
      : `<div class="vide">Aucun titre trouvé pour «\u00A0${echapper(requete)}\u00A0». Essayez une autre orthographe.</div>`;
    zone.hidden = false;
    parId('page').classList.add('a-des-resultats');
    synchroniserCartes();
  }

  async function rechercherTitres(requete) {
    afficherChargement('Recherche en cours…', 'recherche');
    try {
      const resultat = await recupererPistes(requete, 0);
      _rechercheActive = { requete, decalage: TAILLE_PAGE, pistes: resultat.pistes, suivant: resultat.suivant };
      terminerProgression(true);
      masquerStatut();
      definirLancement(false);
      afficherResultatsRecherche();
    } catch (e) {
      afficherErreur('Erreur : ' + e.message);
    }
  }

  async function afficherPlusDeTitres(bouton) {
    bouton.disabled = true;
    bouton.textContent = 'Chargement…';
    try {
      const resultat = await recupererPistes(_rechercheActive.requete, _rechercheActive.decalage);
      const depart = _rechercheActive.pistes.length;
      _rechercheActive.pistes.push(...resultat.pistes);
      _rechercheActive.decalage += TAILLE_PAGE;
      _rechercheActive.suivant = resultat.suivant;
      parId('liste-pistes').insertAdjacentHTML('beforeend', resultat.pistes.map((p, i) => htmlCartePiste(p, depart + i)).join(''));
      synchroniserCartes();
      if (resultat.suivant) { bouton.disabled = false; bouton.textContent = 'Afficher plus de titres'; }
      else bouton.closest('.recherche-pied').remove();
    } catch (e) {
      bouton.disabled = false;
      bouton.textContent = 'Réessayer';
    }
  }

  // ── Suggestions pendant la frappe ────────────────────────────────────────
  const champRecherche     = parId('champ-url');
  const listeSuggestions   = parId('liste-suggestions');
  const cacheSuggestions   = new Map();
  let suggestionsCourantes = [];
  let indiceSuggestion     = -1;
  let minuteurSuggestions  = null;
  let controleurSuggestions = null;

  function fermerSuggestions() {
    clearTimeout(minuteurSuggestions);
    if (controleurSuggestions) controleurSuggestions.abort();
    suggestionsCourantes = [];
    indiceSuggestion = -1;
    listeSuggestions.hidden = true;
    listeSuggestions.innerHTML = '';
    champRecherche.setAttribute('aria-expanded', 'false');
    champRecherche.removeAttribute('aria-activedescendant');
  }

  function activerSuggestion(indice) {
    indiceSuggestion = indice;
    listeSuggestions.querySelectorAll('.suggestion').forEach((element, i) => {
      element.classList.toggle('actif', i === indice);
      element.setAttribute('aria-selected', i === indice);
    });
    if (indice >= 0) champRecherche.setAttribute('aria-activedescendant', 'suggestion-' + indice);
    else champRecherche.removeAttribute('aria-activedescendant');
  }

  function afficherSuggestions(liste, saisie) {
    if (!liste.length) { fermerSuggestions(); return; }
    suggestionsCourantes = liste;
    indiceSuggestion = -1;
    const debut = saisie.toLowerCase();
    listeSuggestions.innerHTML = liste.map((texte, i) => {
      const corps = texte.toLowerCase().startsWith(debut)
        ? `<span class="suggestion-saisie">${echapper(texte.slice(0, saisie.length))}</span><strong>${echapper(texte.slice(saisie.length))}</strong>`
        : `<strong>${echapper(texte)}</strong>`;
      return `<li class="suggestion" id="suggestion-${i}" role="option" aria-selected="false" data-indice="${i}">${icone('loupe')}<span>${corps}</span></li>`;
    }).join('');
    listeSuggestions.hidden = false;
    champRecherche.setAttribute('aria-expanded', 'true');
  }

  async function chargerSuggestions(saisie) {
    const cle = saisie.toLowerCase();
    if (cacheSuggestions.has(cle)) { afficherSuggestions(cacheSuggestions.get(cle), saisie); return; }

    controleurSuggestions = new AbortController();
    try {
      const reponse = await fetch('./sc.php?action=suggestions&q=' + encodeURIComponent(saisie), { signal: controleurSuggestions.signal });
      const resultat = await reponse.json();
      if (!resultat.success) return;
      cacheSuggestions.set(cle, resultat.suggestions);
      if (champRecherche.value.trim() === saisie) afficherSuggestions(resultat.suggestions, saisie);
    } catch (e) { /* suggestions facultatives : on ignore les erreurs et les requêtes annulées */ }
  }

  function choisirSuggestion(texte) {
    champRecherche.value = texte;
    fermerSuggestions();
    rechercherTitres(texte);
  }

  function soumettre() {
    const texte = champRecherche.value.trim();
    if (!texte) { afficherErreur('Saisissez un titre, un artiste ou une adresse SoundCloud.'); return; }
    if (ressembleAUneUrl(texte)) { _rechercheActive = null; lancer(); }
    else rechercherTitres(texte);
  }

  // ── Événements ────────────────────────────────────────────────────────────
  parId('formulaire').addEventListener('submit', e => { e.preventDefault(); soumettre(); });

  champRecherche.addEventListener('input', () => {
    clearTimeout(minuteurSuggestions);
    if (controleurSuggestions) controleurSuggestions.abort();
    majLibelleLancement();
    const saisie = champRecherche.value.trim();
    if (saisie.length < 2 || ressembleAUneUrl(saisie)) { fermerSuggestions(); return; }
    minuteurSuggestions = setTimeout(() => chargerSuggestions(saisie), 200);
  });

  champRecherche.addEventListener('focus', () => {
    const saisie = champRecherche.value.trim();
    const memoire = cacheSuggestions.get(saisie.toLowerCase());
    if (memoire && !ressembleAUneUrl(saisie) && listeSuggestions.hidden) afficherSuggestions(memoire, saisie);
  });

  champRecherche.addEventListener('keydown', e => {
    const nombre = suggestionsCourantes.length;
    const ouvert = !listeSuggestions.hidden && nombre > 0;
    if (e.key === 'ArrowDown' && ouvert)      { e.preventDefault(); activerSuggestion((indiceSuggestion + 1) % nombre); }
    else if (e.key === 'ArrowUp' && ouvert)   { e.preventDefault(); activerSuggestion((indiceSuggestion - 1 + nombre) % nombre); }
    else if (e.key === 'Escape')              { fermerSuggestions(); }
    else if (e.key === 'Enter' && ouvert && indiceSuggestion >= 0) {
      e.preventDefault();
      choisirSuggestion(suggestionsCourantes[indiceSuggestion]);
    }
  });

  listeSuggestions.addEventListener('mousedown', e => e.preventDefault());
  listeSuggestions.addEventListener('click', e => {
    const element = e.target.closest('.suggestion');
    if (element) choisirSuggestion(suggestionsCourantes[element.dataset.indice]);
  });
  document.addEventListener('click', e => { if (!e.target.closest('.recherche-zone')) fermerSuggestions(); });

  parId('resultats-recherche').addEventListener('click', e => {
    const carte = e.target.closest('.carte-piste');
    if (carte && _rechercheActive) {
      const indice = Number(carte.dataset.indice);
      if (e.target.closest('.bouton-ecouter, .carte-pochette')) { ecouterPiste(indice); return; }
      if (e.target.closest('.bouton-analyser')) {
        champRecherche.value = _rechercheActive.pistes[indice].url;
        window.scrollTo({ top: 0, behavior: 'smooth' });
        lancer();
        return;
      }
    }
    const boutonPlus = e.target.closest('#bouton-plus');
    if (boutonPlus) afficherPlusDeTitres(boutonPlus);
  });

  parId('resultats').addEventListener('click', async e => {
    if (e.target.closest('#bouton-retour') && _rechercheActive) {
      detruireLecteur();
      parId('resultats').hidden = true;
      parId('resultats-recherche').hidden = false;
      champRecherche.value = _rechercheActive.requete;
      majLibelleLancement();
      return;
    }
    const bouton = e.target.closest('.bouton-copier');
    if (!bouton) return;
    try { await navigator.clipboard.writeText(bouton.dataset.copie || ''); } catch (_) { return; }
    const libelle  = bouton.querySelector('span');
    const original = libelle.textContent;
    bouton.classList.add('copie');
    libelle.textContent = 'Copié';
    setTimeout(() => { bouton.classList.remove('copie'); libelle.textContent = original; }, 1500);
  });

  let minuteurRedimensionnement = null;
  window.addEventListener('resize', () => {
    clearTimeout(minuteurRedimensionnement);
    minuteurRedimensionnement = setTimeout(genererOndes, 150);
  });

  genererOndes();
  </script>
</body>
</html>