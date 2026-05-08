<style>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        #locationModal.show {
            display: flex !important;
        }

        #locationModal.show>div {
            animation: modalSlideUp 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @keyframes modalSlideUp {
            from {
                opacity: 0;
                transform: translateY(30px) scale(0.97);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @media (min-width: 1024px) {
            #locationModal.show>div {
                animation: modalFadeIn 0.3s ease;
            }

            @keyframes modalFadeIn {
                from {
                    opacity: 0;
                    transform: scale(0.95);
                }

                to {
                    opacity: 1;
                    transform: scale(1);
                }
            }
        }

        #locationMap {
            background: #e8e4df;
        }

        #locationMap .leaflet-container {
            background: #e8e4df;
        }

        .leaflet-control-attribution {
            display: none !important;
        }

        .custom-pin {
            position: relative;
            width: 40px;
            height: 40px;
        }

        .custom-pin::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%) rotate(-45deg);
            width: 28px;
            height: 28px;
            background: #f97316;
            border: 3px solid white;
            border-radius: 50% 50% 50% 0;
            box-shadow: 0 4px 16px rgba(249, 115, 22, 0.5);
            z-index: 2;
        }

        .custom-pin-inner {
            position: absolute;
            bottom: 9px;
            left: 50%;
            transform: translateX(-50%);
            width: 10px;
            height: 10px;
            background: white;
            border-radius: 50%;
            z-index: 3;
        }

        .custom-pin-shadow {
            position: absolute;
            bottom: 2px;
            left: 50%;
            transform: translateX(-50%);
            width: 8px;
            height: 8px;
            background: rgba(0, 0, 0, 0.2);
            border-radius: 50%;
            filter: blur(4px);
            z-index: 1;
        }

        .suggestion-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 16px;
            cursor: pointer;
            transition: background-color 0.15s ease;
        }

        .suggestion-item:hover,
        .suggestion-item.active {
            background-color: hsl(var(--b2, 220 15% 95%));
        }

        .suggestion-item:first-child {
            border-radius: 12px 12px 0 0;
        }

        .suggestion-item:last-child {
            border-radius: 0 0 12px 12px;
        }

        .suggestion-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: hsl(var(--b3, 220 15% 90%));
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .suggestion-content {
            flex: 1;
            min-width: 0;
        }

        .suggestion-title {
            font-size: 14px;
            font-weight: 600;
            line-height: 1.3;
            margin-bottom: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .suggestion-subtitle {
            font-size: 12px;
            color: hsl(var(--bc, 220 15% 40%) / 0.6);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
    html,
    body {
        overflow-x: hidden;
        overflow-y: auto;
        height: auto;
        min-height: 100%;
    }

    [x-cloak] {
        display: none !important;
    }

    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }

    .scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }

    .safe-area-bottom {
        padding-bottom: env(safe-area-inset-bottom);
    }
</style>
