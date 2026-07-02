<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= htmlspecialchars($title ?? 'Sign in') ?> · PharmaCare</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600&family=IBM+Plex+Sans:wght@400;500;600&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        forest: {
                            DEFAULT: '#0F4C3A',
                            light: '#1F7A5C',
                            dark: '#0A362A'
                        },
                        amber: {
                            DEFAULT: '#D9822B',
                            light: '#F2A65A'
                        },
                        clay: {
                            DEFAULT: '#B34A3C'
                        },
                        canvas: '#F6F5F1',
                        surface: '#FFFFFF',
                        ink: {
                            DEFAULT: '#1B231F',
                            muted: '#667169'
                        },
                        line: '#E2E4DE'
                    },

                    fontFamily: {
                        display: [
                            'Fraunces',
                            'ui-serif',
                            'serif'
                        ],

                        sans: [
                            'IBM Plex Sans',
                            'ui-sans-serif',
                            'system-ui'
                        ],

                        mono: [
                            'IBM Plex Mono',
                            'ui-monospace',
                            'monospace'
                        ]
                    }
                }
            }
        }
    </script>

</head>

<body class="bg-canvas text-ink font-sans antialiased">

<div class="min-h-screen lg:grid lg:grid-cols-2">

    <!-- Left branding panel -->

    <div class="relative hidden lg:flex flex-col justify-between bg-forest text-white p-12 overflow-hidden">

        <!-- Decorative background -->

        <svg class="absolute inset-0 w-full h-full opacity-[0.08]"
             viewBox="0 0 400 800"
             preserveAspectRatio="xMidYMid slice">

            <?php for($i = 0; $i < 14; $i++): ?>

                <?php
                    $x = ($i % 4) * 110 - 20;
                    $y = intdiv($i,4) * 160 - 20;

                    $rx = ($i % 4) * 110 + 15;
                    $ry = intdiv($i,4) * 160 + 50;
                ?>

                <rect
                    x="<?= $x ?>"
                    y="<?= $y ?>"
                    width="70"
                    height="140"
                    rx="35"
                    fill="none"
                    stroke="white"
                    stroke-width="2"
                    transform="rotate(20 <?= $rx ?> <?= $ry ?>)"
                />

            <?php endfor; ?>

        </svg>

        <!-- Logo -->

        <div class="relative flex items-center gap-3">

            <svg width="30" height="30" viewBox="0 0 28 28" fill="none">

                <rect
                    x="1"
                    y="1"
                    width="26"
                    height="26"
                    rx="8"
                    stroke="#D9822B"
                    stroke-width="2"
                />

                <path
                    d="M9 14h10M14 9v10"
                    stroke="#D9822B"
                    stroke-width="2"
                    stroke-linecap="round"
                />

            </svg>

            <span class="font-display text-xl">
                Pharma<span class="text-amber">Care</span>
            </span>

        </div>

        <!-- Welcome text -->

        <div class="relative max-w-sm">

            <p class="font-mono text-xs uppercase tracking-widest text-amber mb-3">
                Dispensary Console
            </p>

            <h1 class="font-display text-4xl leading-tight mb-4">
                Every dose, every dispatch, accounted for.
            </h1>

            <p class="text-white/70 text-sm leading-relaxed">
                Sign in to manage inventory, process sales,
                and keep patient records in one place.
            </p>

        </div>

        <p class="relative text-xs text-white/40 font-mono">

            © <?= date('Y') ?> PharmaCare

        </p>

    </div>

    <!-- Right Login Panel -->

    <div class="flex items-center justify-center p-8 sm:p-12">

        <div class="w-full max-w-sm">

            <div class="lg:hidden flex items-center gap-3 mb-10">

                <svg width="26" height="26" viewBox="0 0 28 28" fill="none">

                    <rect
                        x="1"
                        y="1"
                        width="26"
                        height="26"
                        rx="8"
                        stroke="#D9822B"
                        stroke-width="2"
                    />

                    <path
                        d="M9 14h10M14 9v10"
                        stroke="#D9822B"
                        stroke-width="2"
                        stroke-linecap="round"
                    />

                </svg>

                <span class="font-display text-xl text-ink">

                    Pharma<span class="text-amber">Care</span>

                </span>

            </div>

            <!-- Login page goes here -->

            <?php
                if(isset($content))
                {
                    require $content;
                }
            ?>

        </div>

    </div>

</div>

</body>
</html>