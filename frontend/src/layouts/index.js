import SidebarLayout from '../components/layout/sidebar/SidebarLayout.jsx'
import BottomLayout from '../components/layout/bottom/BottomLayout.jsx'

// The numeric `id` is what's stored as `layout_type` in config.json / the
// settings GUI (backend/templates/gui/settings.html.twig) -- keep both in
// sync when adding a layout. Every layout component receives the same
// props: header, content, imageSrc, url.
export const LAYOUTS = {
  sidebar: { id: 0, label: 'Infotext and QR-Code as right column', component: SidebarLayout },
  bottom: { id: 1, label: 'Infotext and QR-Code at bottom', component: BottomLayout },
}

const DEFAULT_LAYOUT = LAYOUTS.sidebar

// Falls back to the default layout for any unrecognized id, so a stray or
// future-invalid layout_type value never blanks the display.
export function getLayoutComponent(layoutTypeId) {
  const entry = Object.values(LAYOUTS).find((layout) => layout.id === layoutTypeId)
  return (entry ?? DEFAULT_LAYOUT).component
}
