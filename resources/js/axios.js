import axios from "axios";
import { router } from "@inertiajs/vue3";

// Add a response interceptor
axios.interceptors.response.use(
  (response) => {
    // Return the response if everything is fine.
    return response;
  },
  (error) => {
    // Check if we have a 401 Unauthorized error
    if (error.response && error.response.status === 401) {
      // Redirect to the login page using Inertia's router
      router.visit(route('login'));
    }
    
    // Always reject the error so it can be handled further if needed.
    return Promise.reject(error);
  }
);
