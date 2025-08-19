<?php

if (!function_exists('user_icon')) {
    /**
     * Gera o HTML para um ícone de usuário circular com iniciais e cor.
     *
     * @param object|null $user
     * @return string
     */
    function user_icon($user, int $size = 32): string
    {
        // Define valores padrão para o caso de o usuário ser nulo.
        $name = 'Usuário Desconhecido';
        $initials = '?';
        $color = '#6c757d'; // Cor cinza padrão

        // Se o objeto do usuário existir, preenche os dados.
        if ($user !== null) {
            $name = $user->name ?? $name;
            $color = !empty($user->color) ? esc($user->color) : $color;

            if (!empty($user->initials)) {
                $initials = esc(strtoupper($user->initials));
            } elseif (!empty($user->name)) {
                $parts = explode(' ', $user->name);
                $initials = count($parts) > 1
                    ? strtoupper(substr($parts[0], 0, 1) . substr(end($parts), 0, 1))
                    : strtoupper(substr($parts[0], 0, 2));
            }
        }

        // Ajusta o tamanho da fonte com base no tamanho do ícone para melhor legibilidade
        $fontSize = '0.8rem'; // Padrão para ícones de 32px ou maiores
        if ($size < 32 && $size >= 24) {
            $fontSize = '0.7rem'; // Para ícones de 24px
        } elseif ($size < 24) {
            $fontSize = '0.6rem'; // Para ícones ainda menores
        }

        $style = "background-color: {$color}; color: #fff; border-radius: 50%; width: {$size}px; height: {$size}px; display: inline-flex; align-items: center; justify-content: center; font-weight: bold; font-size: {$fontSize};";

        return '<div class="user-icon-circle" style="' . $style . '" title="' . esc($name) . '">' . $initials . '</div>';
    }
}