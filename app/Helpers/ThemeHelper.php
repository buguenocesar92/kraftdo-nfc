<?php

namespace App\Helpers;

class ThemeHelper
{
    public static function getThemeConfig($theme = 'love')
    {
        $themes = [
            'love' => [
                'name' => 'Amor',
                'emoji' => '💕',
                'header_icon' => '💝',
                'video_icon' => '💞',
                'audio_icon' => '🎵',
                'gallery_icon' => '💖',
                'colors' => [
                    'primary' => 'from-pink-500 to-rose-600',
                    'secondary' => 'from-rose-400 to-pink-500',
                    'accent' => 'text-pink-600',
                    'gradient_bg' => '#ec4899, #f43f5e, #be185d',
                    'section_bg' => 'from-pink-50 to-rose-50',
                    'section_border' => 'border-pink-200',
                    'section_text' => 'from-pink-600 via-rose-600 to-red-600',
                ]
            ],
            'birthday' => [
                'name' => 'Cumpleaños',
                'emoji' => '🎂',
                'header_icon' => '🎁',
                'video_icon' => '🎉',
                'audio_icon' => '🎶',
                'gallery_icon' => '🎊',
                'colors' => [
                    'primary' => 'from-yellow-500 to-orange-600',
                    'secondary' => 'from-orange-400 to-red-500',
                    'accent' => 'text-orange-600',
                    'gradient_bg' => '#facc15, #f97316, #ef4444',
                    'section_bg' => 'from-yellow-50 to-orange-50',
                    'section_border' => 'border-orange-200',
                    'section_text' => 'from-yellow-600 via-orange-600 to-red-600',
                ]
            ],
            'anniversary' => [
                'name' => 'Aniversario',
                'emoji' => '💒',
                'header_icon' => '💍',
                'video_icon' => '💫',
                'audio_icon' => '🎼',
                'gallery_icon' => '💖',
                'colors' => [
                    'primary' => 'from-amber-500 to-yellow-600',
                    'secondary' => 'from-yellow-400 to-amber-500',
                    'accent' => 'text-amber-600',
                    'gradient_bg' => '#fbbf24, #eab308, #f97316',
                    'section_bg' => 'from-amber-50 to-yellow-50',
                    'section_border' => 'border-amber-200',
                    'section_text' => 'from-amber-600 via-yellow-600 to-orange-600',
                ]
            ],
            'friendship' => [
                'name' => 'Amistad',
                'emoji' => '🤝',
                'header_icon' => '🎁',
                'video_icon' => '🌟',
                'audio_icon' => '🎵',
                'gallery_icon' => '📸',
                'colors' => [
                    'primary' => 'from-blue-500 to-teal-600',
                    'secondary' => 'from-teal-400 to-green-500',
                    'accent' => 'text-blue-600',
                    'gradient_bg' => '#3b82f6, #14b8a6, #10b981',
                    'section_bg' => 'from-blue-50 to-teal-50',
                    'section_border' => 'border-blue-200',
                    'section_text' => 'from-blue-600 via-teal-600 to-green-600',
                ]
            ],
            'graduation' => [
                'name' => 'Graduación',
                'emoji' => '🎓',
                'header_icon' => '🏆',
                'video_icon' => '📚',
                'audio_icon' => '🎵',
                'gallery_icon' => '📜',
                'colors' => [
                    'primary' => 'from-indigo-500 to-purple-600',
                    'secondary' => 'from-purple-400 to-indigo-500',
                    'accent' => 'text-indigo-600',
                    'gradient_bg' => '#6366f1, #8b5cf6, #3b82f6',
                    'section_bg' => 'from-indigo-50 to-purple-50',
                    'section_border' => 'border-indigo-200',
                    'section_text' => 'from-indigo-600 via-purple-600 to-blue-600',
                ]
            ],
            'christmas' => [
                'name' => 'Navidad',
                'emoji' => '🎄',
                'header_icon' => '🎅',
                'video_icon' => '⭐',
                'audio_icon' => '🔔',
                'gallery_icon' => '🎄',
                'colors' => [
                    'primary' => 'from-red-500 to-green-600',
                    'secondary' => 'from-green-400 to-red-500',
                    'accent' => 'text-red-600',
                    'gradient_bg' => '#ef4444, #22c55e, #dc2626',
                    'section_bg' => 'from-red-50 to-green-50',
                    'section_border' => 'border-red-200',
                    'section_text' => 'from-red-600 via-green-600 to-red-600',
                ]
            ],
            'valentine' => [
                'name' => 'San Valentín',
                'emoji' => '💖',
                'header_icon' => '💘',
                'video_icon' => '💕',
                'audio_icon' => '💓',
                'gallery_icon' => '💝',
                'colors' => [
                    'primary' => 'from-rose-500 to-pink-600',
                    'secondary' => 'from-pink-400 to-rose-500',
                    'accent' => 'text-rose-600',
                    'gradient_bg' => '#f43f5e, #ec4899, #e11d48',
                    'section_bg' => 'from-rose-50 to-pink-50',
                    'section_border' => 'border-rose-200',
                    'section_text' => 'from-rose-600 via-pink-600 to-red-600',
                ]
            ],
            'mother_day' => [
                'name' => 'Día de la Madre',
                'emoji' => '🌸',
                'header_icon' => '💐',
                'video_icon' => '🌺',
                'audio_icon' => '🎵',
                'gallery_icon' => '🌷',
                'colors' => [
                    'primary' => 'from-pink-500 to-purple-600',
                    'secondary' => 'from-purple-400 to-pink-500',
                    'accent' => 'text-pink-600',
                    'gradient_bg' => '#ec4899, #a855f7, #f43f5e',
                    'section_bg' => 'from-pink-50 to-purple-50',
                    'section_border' => 'border-pink-200',
                    'section_text' => 'from-pink-600 via-purple-600 to-rose-600',
                ]
            ],
            'father_day' => [
                'name' => 'Día del Padre',
                'emoji' => '👔',
                'header_icon' => '🏆',
                'video_icon' => '⚡',
                'audio_icon' => '🎵',
                'gallery_icon' => '📸',
                'colors' => [
                    'primary' => 'from-gray-500 to-blue-600',
                    'secondary' => 'from-blue-400 to-gray-500',
                    'accent' => 'text-blue-600',
                    'gradient_bg' => '#6b7280, #3b82f6, #64748b',
                    'section_bg' => 'from-gray-50 to-blue-50',
                    'section_border' => 'border-gray-200',
                    'section_text' => 'from-gray-600 via-blue-600 to-slate-600',
                ]
            ],
            'congratulations' => [
                'name' => 'Felicitaciones',
                'emoji' => '🎉',
                'header_icon' => '🏆',
                'video_icon' => '⭐',
                'audio_icon' => '🎶',
                'gallery_icon' => '🎊',
                'colors' => [
                    'primary' => 'from-emerald-500 to-blue-600',
                    'secondary' => 'from-blue-400 to-emerald-500',
                    'accent' => 'text-emerald-600',
                    'gradient_bg' => '#10b981, #3b82f6, #8b5cf6',
                    'section_bg' => 'from-emerald-50 to-blue-50',
                    'section_border' => 'border-emerald-200',
                    'section_text' => 'from-emerald-600 via-blue-600 to-purple-600',
                ]
            ],
        ];

        return $themes[$theme] ?? $themes['love'];
    }

    public static function getAllThemes()
    {
        return [
            'love' => '💕 Amor',
            'birthday' => '🎂 Cumpleaños',
            'anniversary' => '💒 Aniversario',
            'friendship' => '🤝 Amistad',
            'graduation' => '🎓 Graduación',
            'christmas' => '🎄 Navidad',
            'valentine' => '💖 San Valentín',
            'mother_day' => '🌸 Día de la Madre',
            'father_day' => '👔 Día del Padre',
            'congratulations' => '🎉 Felicitaciones',
        ];
    }
}