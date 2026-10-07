<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>neriahpro.com - AI-Shield Cyber Defense Interception</title>
    <!-- Local Fonts (Zero External Latency) -->
    <link rel="stylesheet" href="{{ asset('fonts/instrument-sans/instrument-sans.css') }}">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            background-color: #09090b;
            color: #f4f4f5;
            font-family: 'Instrument Sans', ui-sans-serif, system-ui, -apple-system, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            position: relative;
            overflow-x: hidden;
        }
        .grid-bg {
            position: absolute;
            inset: 0;
            background-image: 
                linear-gradient(to right, rgba(255, 255, 255, 0.03) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
            background-size: 32px 32px;
            pointer-events: none;
        }
        .container {
            max-width: 680px;
            width: 100%;
            background: #18181b;
            border: 1px solid #dc2626;
            box-shadow: 0 0 50px rgba(220, 38, 38, 0.15);
            position: relative;
            z-index: 10;
        }
        .header {
            padding: 1rem 1.5rem;
            background: rgba(220, 38, 38, 0.1);
            border-bottom: 1px solid rgba(220, 38, 38, 0.3);
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #ef4444;
            font-weight: 700;
        }
        .content {
            padding: 2rem 1.75rem;
        }
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.25rem 0.65rem;
            background: rgba(220, 38, 38, 0.15);
            border: 1px solid rgba(220, 38, 38, 0.4);
            color: #f87171;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 1.25rem;
        }
        .pulse-dot {
            width: 6px;
            height: 6px;
            background-color: #ef4444;
            animation: pulse 1.5s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.4; transform: scale(0.8); }
        }
        h1 {
            font-size: 1.5rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: -0.02em;
            color: #ffffff;
            margin-bottom: 0.75rem;
            line-height: 1.2;
        }
        p {
            font-size: 0.875rem;
            color: #a1a1aa;
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }
        .terminal {
            background: #09090b;
            border: 1px solid #27272a;
            padding: 1.25rem;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.775rem;
            color: #d4d4d8;
            margin-bottom: 1.75rem;
        }
        .terminal-row {
            display: flex;
            justify-content: space-between;
            padding: 0.35rem 0;
            border-bottom: 1px dashed #1f1f23;
        }
        .terminal-row:last-child {
            border-bottom: none;
        }
        .terminal-key {
            color: #71717a;
            text-transform: uppercase;
        }
        .terminal-val {
            color: #ef4444;
            font-weight: 600;
        }
        .terminal-val.safe {
            color: #10b981;
        }
        .actions {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }
        .btn {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 0.75rem 1.25rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s ease;
            cursor: pointer;
        }
        .btn-primary {
            background: #27272a;
            color: #f4f4f5;
            border: 1px solid #3f3f46;
        }
        .btn-primary:hover {
            background: #3f3f46;
            color: #ffffff;
        }
        .footer-note {
            margin-top: 1.5rem;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.7rem;
            color: #52525b;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="grid-bg"></div>

    <div class="container">
        <div class="header">
            <span>NERIAH PRO // CYBER SHIELD</span>
            <span>HTTP 403 FORBIDDEN</span>
        </div>

        <div class="content">
            <div class="badge">
                <span class="pulse-dot"></span>
                <span>Active Threat Sandbox Interception</span>
            </div>

            <h1>Permintaan Akses Diblokir</h1>
            <p>
                Sistem pertahanan otonom <strong>AI-Threat Shield</strong> mendeteksi muatan payload berbahaya, pola probing otomatis tak wajar, atau percobaan eksploitasi remote code execution (RCE).
            </p>

            <div class="terminal">
                <div class="terminal-row">
                    <span class="terminal-key">Incident Reference ID</span>
                    <span class="terminal-val">{{ $incidentId ?? 'CYB-' . time() }}</span>
                </div>
                <div class="terminal-row">
                    <span class="terminal-key">Client Source IP</span>
                    <span class="terminal-val">{{ $ip ?? '127.0.0.1' }}</span>
                </div>
                <div class="terminal-row">
                    <span class="terminal-key">Detected Threat Vector</span>
                    <span class="terminal-val">{{ $threatType ?? 'anomalous_probe' }}</span>
                </div>
                <div class="terminal-row">
                    <span class="terminal-key">Cumulative Strike Counter</span>
                    <span class="terminal-val">{{ $strikes ?? 1 }} / 3</span>
                </div>
                <div class="terminal-row">
                    <span class="terminal-key">Firewall Isolation Status</span>
                    <span class="terminal-val {{ ($isBlocked ?? false) ? '' : 'safe' }}">
                        {{ ($isBlocked ?? false) ? 'ISOLATED (IP BANNED)' : 'PROBING LOGGED' }}
                    </span>
                </div>
                <div class="terminal-row">
                    <span class="terminal-key">Defense Protocol</span>
                    <span class="terminal-val safe">AI-Shield Monolith Engine v13</span>
                </div>
            </div>

            <div class="actions">
                <a href="/" class="btn btn-primary">
                    ← Kembali ke Beranda Utama
                </a>
            </div>

            <div class="footer-note">
                Security Policy Enforced • O(1) Distributed Ledger Protection • Neriah Pro Enterprise
            </div>
        </div>
    </div>
</body>
</html>
