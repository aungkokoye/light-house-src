# AI Chat

An AI-powered chat widget for LightHouse staff. Allows super admins to ask questions about the company — the AI answers using a knowledge base stored in the database.

---

## How It Works

```
User types a message
    │
    ▼
AiChatWidget.vue  (Vue component — floating button + panel)
    │  POST /api/chat/stream  (Bearer token)
    ▼
ChatController::stream()
    │  authorize('chat.stream') — must be admin + super permission + AI_CHAT_ENABLED=true
    ▼
ChatKnowledgeManager::buildSystemPrompt()
    │  loads all active rows from chat_knowledge table
    │  groups by category, formats as markdown sections
    ▼
OpenAI API (gpt-4o-mini)
    │  system prompt = knowledge base
    │  user messages = conversation history + new message
    ▼
Server-Sent Events (SSE) streamed back token by token
    │
    ▼
AiChatWidget.vue renders tokens as they arrive
```

---

## Access Control

The chat widget is only visible and usable when **all three** conditions are met:

| Condition | Where enforced |
|-----------|---------------|
| `AI_CHAT_ENABLED=true` in `.env` | Frontend (`window.__AI_CHAT_ENABLED__`) + Backend (`ChatPolicy`) |
| User has `admin` role | Frontend + Backend (`ChatPolicy`) |
| User has `super` permission | Frontend + Backend (`ChatPolicy`) |

Any user missing any of these conditions will not see the chat button and will receive a `403` if they call the API directly.

---

## Environment Variables

Add these to your `.env` file:

```env
AI_CHAT_ENABLED=true           # Set to false to hide the widget for everyone
AI_API_KEY=sk-proj-xxxx        # Your OpenAI API key
AI_MODEL=gpt-4o-mini           # Model to use (gpt-4o-mini recommended)
AI_BASE_URI=https://api.openai.com/v1/  # OpenAI base URL
```

After changing `.env` on the production server, run:

```bash
sudo -u www-data php /var/www/light-house-src/artisan config:clear
sudo -u www-data php /var/www/light-house-src/artisan optimize
```

### Getting an OpenAI API Key

1. Go to **platform.openai.com**
2. Sign up or log in
3. Click your profile → **API keys** → **Create new secret key**
4. Copy the key — you only see it once
5. Add a payment method at **platform.openai.com/settings/billing**

> `gpt-4o-mini` costs approximately $0.15 per 1 million input tokens. A typical conversation costs less than $0.001.

---

## Knowledge Base

All content the AI uses to answer questions is stored in the `chat_knowledge` table. The AI **only answers from this table** — it will not make up information.

### Table Structure

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key |
| `category` | string | Groups related entries (e.g. "Services", "Contact") |
| `title` | string | Short name for this entry |
| `content` | text | The actual information the AI uses |
| `active` | boolean | `true` = included in AI prompt, `false` = excluded |
| `sort_order` | integer | Controls order within a category (lower = first) |
| `created_by` | bigint | FK → users |
| `created_at` | timestamp | — |
| `updated_at` | timestamp | — |

### How the Prompt Is Built

At request time, all `active = true` rows are loaded, grouped by `category`, and formatted like:

```
## Services

### Offset Printing
[content]

### Digital Printing
[content]

## Contact

### How to Reach Us
[content]
```

This is injected as the system prompt before the conversation history.

---

## Managing the Knowledge Base

### Via Admin UI

Navigate to **Dashboard → Chat Knowledge** (`/admin/chat-knowledge`).

Only users with `admin` role + `super` permission can access this page.

**Create a new entry:**
1. Click **New Entry**
2. Fill in Category, Title, and Content
3. Set Sort Order (lower numbers appear first in the prompt)
4. Toggle **Active** on to include it immediately
5. Click **Create Entry**

**Edit an entry:**
- Click the edit icon on any row
- Update fields and click **Save Changes**

**Deactivate an entry:**
- Edit the entry and toggle **Active** off
- The AI will stop using it immediately (no restart needed)

**Delete an entry:**
- Click the delete icon → confirm
- Permanent — cannot be undone

---

## Pre-loaded Knowledge

The migration `2026_04_23_000002_seed_chat_knowledge_table.php` seeds the database with initial company knowledge across these categories:

| Category | Entries |
|----------|---------|
| Overview | About LightHouse, Our Mission |
| Services | All Services Overview, Offset Printing, Digital Printing, Large-Format, Packaging, Books & Catalogues, Finishing Services, Products We Print, Design File Support |
| Locations | All 4 sites (Yangon, Mandalay, Naypyidaw, Bago) with phone numbers |
| Business Hours | Opening Hours (Mon–Sat 9AM–6PM), Public Holidays |
| Contact | How to Reach Us (phone, email, Messenger), Customer Registration |
| Staff | Staff Positions (all 7 roles) |
| Orders | How to Place an Order, File Requirements |
| FAQ | Min order qty, Turnaround times, Delivery, Payment methods, Proofs |

To apply this seed on a fresh install, just run:

```bash
php artisan migrate
```

To re-seed on an existing database (if you rolled back):

```bash
php artisan migrate:rollback --step=1
php artisan migrate
```

> **Note:** The seed migration only deletes rows where `created_by IS NULL` on rollback — entries added through the admin UI are preserved.

---

## Writing Good Knowledge Entries

| Tip | Example |
|-----|---------|
| Keep one topic per entry | Don't mix "Pricing" and "Delivery" in one entry |
| Use plain language | The AI will paraphrase — don't over-format |
| Be specific with numbers | "3–7 business days" not "a few days" |
| Include contact details in relevant entries | Helps AI suggest contacting us when unsure |
| Use sort_order to prioritise | Important info = lower number = appears earlier in prompt |

---

## When the AI Doesn't Know

If a customer asks something not in the knowledge base, the AI is instructed to respond politely and provide contact details:

> *"I'm sorry, I don't have that information. Please contact us directly and our team will be happy to help:*
> - *Phone: +95 9 782 275275*
> - *Email: info@lighthouse-print.com*
> - *Messenger: Lighthouse Printing - Myanmar"*

To reduce these responses, add a new knowledge entry covering the topic.

---

## Key Files

| File | Purpose |
|------|---------|
| `resources/js/components/AiChatWidget.vue` | Floating chat button + panel, SSE streaming |
| `app/Http/Controllers/Ai/ChatController.php` | Stream endpoint, calls OpenAI API |
| `app/Services/ChatKnowledgeManager.php` | Builds system prompt from DB, CRUD logic |
| `app/Repositories/ChatKnowledgeRepository.php` | DB queries |
| `app/Filters/ChatKnowledgeFilter.php` | Search, category, active, sort filters |
| `app/Policies/ChatPolicy.php` | Gates the stream endpoint (admin + super + enabled) |
| `app/Policies/ChatKnowledgePolicy.php` | Gates CRUD (admin + super) |
| `config/ai.php` | Reads AI env vars |
| `database/migrations/2026_04_23_000001_create_chat_knowledge_table.php` | Table schema |
| `database/migrations/2026_04_23_000002_seed_chat_knowledge_table.php` | Initial knowledge data |
| `light-house-docker/php-apache/Dockerfile` | PHP streaming config (`output_buffering = Off`) |
| `light-house-docker/php-apache/vhost.config` | Apache SSE config (gzip disabled for stream route) |

---

## Disabling the Chat

To hide the chat widget from everyone without deleting anything:

```env
AI_CHAT_ENABLED=false
```

Then run:

```bash
sudo -u www-data php /var/www/light-house-src/artisan optimize:clear
sudo -u www-data php /var/www/light-house-src/artisan optimize
```

The widget will disappear immediately for all users.
