# Weather API Proxy Server

**A lightweight PHP proxy that keeps OpenWeatherMap and ipify API keys server-side — the browser never sees them.**

Built as the backend for [AdvancedWeatherFetcher](https://github.com/AnastaCodes/AdvancedWeatherFetcher): instead of shipping API keys in client-side JavaScript (where anyone can read them), the frontend calls this proxy and the proxy injects the keys from environment variables.

## Architecture

```
Browser (AdvancedWeatherFetcher) ──▶ this proxy (PHP + cURL) ──▶ OpenWeatherMap / ipify
```

## How it works

- API keys are read from environment variables (`OPENWEATHERMAP_API_KEY`, `IPIFY_API_KEY`) — never committed, never sent to the client
- Two routes: `openweathermap` (proxied to `api.openweathermap.org/data/2.5/*`) and `ipify` (proxied to `geo.ipify.org/api/v2/*`)
- CORS headers restrict browser access to the known frontend origins
- Responses are passed through as JSON

Example request:

```
GET /?route=openweathermap&url=weather%3Fq%3DBerlin%26units%3Dmetric
→ proxied to api.openweathermap.org/data/2.5/weather?q=Berlin&units=metric&appid=<server-side key>
```

## Deploy your own

1. Host `index.php` on any PHP-capable server (the original instance ran on Adaptable.app, which has since shut down)
2. Set the two environment variables with your API keys
3. Update the allowed origins in `setCorsHeaders()` to match your frontend

## Tech stack

PHP · cURL · OpenWeatherMap API · ipify API

## Acknowledgments

- Weather data by [OpenWeatherMap](https://openweathermap.org/), IP lookup by [ipify](https://www.ipify.org/).
