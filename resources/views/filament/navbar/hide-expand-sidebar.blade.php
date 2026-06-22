<style>
    /*
     * Top navigation is used on desktop (>= 1024px / Filament's `lg`),
     * so the sidebar-toggle hamburger is redundant there and stays hidden.
     *
     * Below `lg`, Filament hides the top-nav links (`.fi-topbar-nav-groups`
     * is `hidden lg:flex`), so we MUST keep the hamburger visible so mobile
     * and tablet users can open the off-canvas sidebar drawer for navigation.
     */
    @media (min-width: 1024px) {
        .fi-topbar-open-sidebar-btn,
        .fi-topbar-close-sidebar-btn {
            display: none !important;
        }
    }
</style>
