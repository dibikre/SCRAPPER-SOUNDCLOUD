import asyncio
import logging
from centrifuge import (
    Client,
    ClientEventHandler,
    SubscriptionEventHandler,
    ConnectedContext,
    ConnectingContext,
    DisconnectedContext,
    ErrorContext,
    SubscribedContext,
    SubscribingContext,
    UnsubscribedContext,
    PublicationContext,
)

# Pour voir les logs détaillés (équivalent du debug=True côté JS)
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger("centrifuge")
logger.setLevel(logging.DEBUG)


# === Handlers globaux du client ===
class MyClientEventHandler(ClientEventHandler):
    async def on_connected(self, ctx: ConnectedContext) -> None:
        print("✅ Connecté avec succès !", ctx)

    async def on_connecting(self, ctx: ConnectingContext) -> None:
        print("⏳ Connexion en cours...", ctx)

    async def on_disconnected(self, ctx: DisconnectedContext) -> None:
        print("🔌 Déconnecté :", ctx)

    async def on_error(self, ctx: ErrorContext) -> None:
        print("❌ Erreur client :", ctx)


# === Handlers de la subscription (ex: channel chat) ===
class MySubscriptionEventHandler(SubscriptionEventHandler):
    def __init__(self, channel: str):
        self.channel = channel

    async def on_subscribed(self, ctx: SubscribedContext) -> None:
        print(f"✅ Abonné au channel {self.channel}")

    async def on_subscribing(self, ctx: SubscribingContext) -> None:
        print(f"⏳ Abonnement en cours sur {self.channel}...")

    async def on_unsubscribed(self, ctx: UnsubscribedContext) -> None:
        print(f"🚫 Désabonné de {self.channel}")

    async def on_publication(self, ctx: PublicationContext) -> None:
        print("📨 Message reçu :", ctx.pub.data)

    async def on_error(self, ctx: ErrorContext) -> None:
        print(f"❌ Erreur subscription sur {self.channel} :", ctx)


async def main() -> None:
    client = Client(
        "wss://websocket-extensions-v6.stripchat.com/connection/websocket",
        events=MyClientEventHandler(),
        token="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIwIiwiaW5mbyI6eyJpc0d1ZXN0IjpmYWxzZSwidXNlcklkIjowfX0.xuVh8dWD5lQ0SP_1oLxte3BPzliJly_hMevLh8eNDHE",
        # headers si besoin pour Cloudflare / Origin
        headers={
            "Origin": "https://fr.stripchat.com",
            "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:152.0) Gecko/20100101 Firefox/152.0",
        },
    )

    # === Abonnement à un channel (exemple chat) ===
    channel = "chat:public:Eclypse1"  # ← Change avec le vrai nom
    sub = client.new_subscription(
        channel,
        events=MySubscriptionEventHandler(channel),
    )

    # Lancement
    await client.connect()
    await sub.subscribe()

    print("🚀 Scraping en cours... (Ctrl+C pour arrêter)")
    try:
        await asyncio.sleep(3600)  # Garde le script vivant
    except asyncio.CancelledError:
        pass
    finally:
        await client.disconnect()


if __name__ == "__main__":
    asyncio.run(main())