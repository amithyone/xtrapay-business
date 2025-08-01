# Telegram Notification Setup Guide

## Step 1: Create/Verify Your Bot
1. Go to [@BotFather](https://t.me/botfather) on Telegram
2. Create a new bot or use existing bot: `@xtarbuinessbot`
3. Get your bot token (you already have: `7226795329:AAFKGJNIL6_EOjs1ZUBEWUM_RS9U848QmT4`)

## Step 2: Get Your Chat ID
1. Go to [@userinfobot](https://t.me/userinfobot) on Telegram
2. Send any message to @userinfobot
3. It will reply with your chat ID
4. Copy the chat ID (it should be a number like `123456789`)

## Step 3: Start Chat with Your Bot
1. Go to your bot: [@xtarbuinessbot](https://t.me/xtarbuinessbot)
2. Click "Start" or send `/start`
3. The bot should respond with a welcome message

## Step 4: Update Your Settings
1. Go to http://localhost:8000/notifications
2. Enter your bot token: `7226795329:AAFKGJNIL6_EOjs1ZUBEWUM_RS9U848QmT4`
3. Enter the chat ID you got from @userinfobot
4. Click "Save Settings"

## Step 5: Test the Notification
1. After saving, the system will automatically test the connection
2. You should receive a test message in your Telegram chat
3. If successful, you'll see "Notification settings saved!" message

## Troubleshooting
- **"chat not found"**: Make sure you've started a chat with the bot
- **"bot token invalid"**: Check your bot token with @BotFather
- **"chat ID wrong"**: Get a fresh chat ID from @userinfobot

## Current Settings
- Bot Token: `7226795329:AAFKGJNIL6_EOjs1ZUBEWUM_RS9U848QmT4`
- Current Chat ID: `6859958780` (needs verification)
- Bot Username: `@xtarbuinessbot` 