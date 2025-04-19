import { usePage } from "@inertiajs/vue3";

// Expected Config from `HandleInertiaRequest.php`
// - COMMENT_MAX_DEPTH
// - COMMENT_PAGINATION_LIMIT

export function configData() {
  return usePage().props.config;
}