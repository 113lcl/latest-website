from telegram import Update
from telegram.ext import ApplicationBuilder, CommandHandler, ContextTypes
import os

async def start(update: Update, context: ContextTypes.DEFAULT_TYPE):
    await update.message.reply_text("Бот работает! Добро пожаловать.")

if __name__ == '__main__':
    # Получаем токен из переменной окружения BOT_TOKEN
    token = os.getenv("BOT_TOKEN")
    if not token:
        print("Ошибка: переменная окружения BOT_TOKEN не установлена.")
    else:
        app = ApplicationBuilder().token(token).build()
        app.add_handler(CommandHandler("start", start))
        print("Бот запущен. Ожидание сообщений...")
        app.run_polling()
