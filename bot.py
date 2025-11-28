from telegram import Update
from telegram.ext import ApplicationBuilder, CommandHandler, ContextTypes
import os

async def start(update: Update, context: ContextTypes.DEFAULT_TYPE):
    await update.message.reply_text("Bot is working! Welcome.")

if __name__ == '__main__':
    
    token = os.getenv("BOT_TOKEN")
    if not token:
        print("Error: Environment variable BOT_TOKEN is not set.")
    else:
        app = ApplicationBuilder().token(token).build()
        app.add_handler(CommandHandler("start", start))
        print("The bot has been launched. Waiting for messages...")
        app.run_polling()
