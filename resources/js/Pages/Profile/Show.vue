<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import ConnectedAccountsForm from '@/Pages/Profile/Partials/ConnectedAccountsForm.vue';
import DeleteUserForm from '@/Pages/Profile/Partials/DeleteUserForm.vue';
import LogoutOtherBrowserSessionsForm from '@/Pages/Profile/Partials/LogoutOtherBrowserSessionsForm.vue';
import SectionBorder from '@/Components/SectionBorder.vue';
import SetPasswordForm from '@/Pages/Profile/Partials/SetPasswordForm.vue';
import TwoFactorAuthenticationForm from '@/Pages/Profile/Partials/TwoFactorAuthenticationForm.vue';
import UpdatePasswordForm from '@/Pages/Profile/Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from '@/Pages/Profile/Partials/UpdateProfileInformationForm.vue';

defineProps({
    confirmsTwoFactorAuthentication: Boolean,
    sessions: Array,
});
</script>

<template>
    <AppLayout title="Profile">
        <!-- Background and padding -->
    <div class="bg-gradient-to-br from-background to-background/50 dark:from-gray-950 dark:to-gray-900 min-h-screen py-12 px-4 sm:px-6 lg:px-8">
            <div class="max-w-5xl mx-auto space-y-8">
                <!-- Profile Info -->
                <div
                    v-if="$page.props.jetstream.canUpdateProfileInformation"
                    class="bg-white dark:bg-gray-900 shadow-md rounded-2xl p-6 border border-transparent dark:border-gray-800"
                >
                    <UpdateProfileInformationForm :user="$page.props.auth.user" />
                </div>

                <!-- Update Password -->
                <div
                    v-if="$page.props.jetstream.canUpdatePassword && $page.props.socialstream.hasPassword"
                    class="bg-white dark:bg-gray-900 shadow-md rounded-2xl p-6 border border-transparent dark:border-gray-800"
                >
                    <UpdatePasswordForm />
                </div>

                <!-- Set Password -->
                <div
                    v-else
                    class="bg-white dark:bg-gray-900 shadow-md rounded-2xl p-6 border border-transparent dark:border-gray-800"
                >
                    <SetPasswordForm />
                </div>

                <!-- Two-Factor Auth -->
                <div
                    v-if="$page.props.jetstream.canManageTwoFactorAuthentication && $page.props.socialstream.hasPassword"
                    class="bg-white dark:bg-gray-900 shadow-md rounded-2xl p-6 border border-transparent dark:border-gray-800"
                >
                    <TwoFactorAuthenticationForm :requires-confirmation="confirmsTwoFactorAuthentication" />
                </div>

                <!-- Connected Accounts -->
                <div
                    v-if="$page.props.socialstream.show"
                    class="bg-white dark:bg-gray-900 shadow-md rounded-2xl p-6 border border-transparent dark:border-gray-800"
                >
                    <ConnectedAccountsForm />
                </div>

                <!-- Logout Sessions -->
                <div
                    v-if="$page.props.socialstream.hasPassword"
                    class="bg-white dark:bg-gray-900 shadow-md rounded-2xl p-6 border border-transparent dark:border-gray-800"
                >
                    <LogoutOtherBrowserSessionsForm :sessions="sessions" />
                </div>

                <!-- Account Deletion -->
                <div
                    v-if="$page.props.jetstream.hasAccountDeletionFeatures && $page.props.socialstream.hasPassword"
                    class="bg-white dark:bg-gray-900 shadow-md rounded-2xl p-6 border border-transparent dark:border-gray-800"
                >
                    <DeleteUserForm />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
