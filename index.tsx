// Import Hubleto Core (Hubleto Main).
import './src/Main'

// Import Hubleto Community Apps
import './apps/community/Loader'

// Render react elements into HTML body
// This method parses the DOM and tries to find all "<app-*" elements.
// For each of these elements renders an appropriate React component
// from the list ofcomponents registered by `registerReactComponent()`.

globalThis.main.renderReactElements();
