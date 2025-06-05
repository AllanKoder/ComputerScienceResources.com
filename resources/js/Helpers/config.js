import { usePage } from "@inertiajs/vue3";

// Expected Config from `HandleInertiaRequest.php`
// - COMMENT_MAX_DEPTH
// - COMMENT_PAGINATION_LIMIT

export function getConfigData() {
  return usePage().props.config;
}
