<?php

    namespace App\Enums;

    enum ServiceType: int
    {
        case Revisao = 1;
        case TrocaOleo = 2;
        case TrocaPneu = 3;
        case Freio = 4;
        case Suspensao = 5;
        case Eletrica = 6;
        case Outro = 7;

        public function label(): string
        {
            return match ($this) {
                self::Revisao => 'Revisão',
                self::TrocaOleo => 'Troca de óleo',
                self::TrocaPneu => 'Troca de pneu',
                self::Freio => 'Freio',
                self::Suspensao => 'Suspensão',
                self::Eletrica => 'Elétrica',
                self::Outro => 'Outro',
            };
        }

        public static function options(): array
        {
            return array_map(
                fn (self $case) => ['id' => $case->value, 'name' => $case->label()],
                self::cases()
            );
        }
    }
