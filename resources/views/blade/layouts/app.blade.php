<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Chat') }} - @yield('title', 'Chat')</title>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --ease-out: cubic-bezier(0.2, 0, 0, 1);
            --ease-in-out: cubic-bezier(0.77, 0, 0.175, 1);
            --duration-quick: 120ms;
            --duration-standard: 250ms;
            --duration-slow: 400ms;
        }

        .btn-press { transition: transform var(--duration-quick) var(--ease-out); }
        .btn-press:active { transform: scale(0.97); }

        .animate-fade-in {
            animation: fadeSlideIn var(--duration-standard) var(--ease-out) both;
        }

        @keyframes fadeSlideIn {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes messageIn {
            from { opacity: 0; transform: translateY(4px) scale(0.98); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        .message-enter {
            animation: messageIn 200ms var(--ease-out) both;
        }

        @keyframes badgePop {
            0% { transform: scale(0.5); opacity: 0; }
            70% { transform: scale(1.15); }
            100% { transform: scale(1); opacity: 1; }
        }
        .badge-pop { animation: badgePop 300ms cubic-bezier(0.175, 0.885, 0.32, 1.275) both; }

        @keyframes typingPulse {
            0%, 60%, 100% { transform: translateY(0); opacity: 0.4; }
            30% { transform: translateY(-4px); opacity: 1; }
        }
        .typing-dot { animation: typingPulse 1.4s ease-in-out infinite; }
        .typing-dot:nth-child(2) { animation-delay: 0.2s; }
        .typing-dot:nth-child(3) { animation-delay: 0.4s; }

        .scrollbar-thin::-webkit-scrollbar { width: 6px; }
        .scrollbar-thin::-webkit-scrollbar-track { background: transparent; }
        .scrollbar-thin::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.1); border-radius: 3px; }
        .scrollbar-thin::-webkit-scrollbar-thumb:hover { background: rgba(0,0,0,0.2); }

        .hidden-el { display: none !important; }
    </style>
</head>
<body class="h-full bg-gray-100 antialiased font-sans">
    @yield('content')

    @stack('scripts')
</body>
</html>
