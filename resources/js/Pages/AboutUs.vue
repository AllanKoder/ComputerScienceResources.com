<script setup>
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import FrequentlyAskedQuestion from "@/Components/FrequentlyAskedQuestion.vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import { Head } from "@inertiajs/vue3";
import { canonicalFor, defaultOgImage, SITE_NAME } from "@/Helpers/seo";
</script>

<template>
    <AppLayout title="About Us">
        <!-- Page metadata -->
        <Head>
            <meta head-key="about:og:title" property="og:title" :content="`About Us - ${SITE_NAME}`" />
            <meta head-key="about:description" name="description" content="Learn about ComputerScienceResources.com — our mission to organize the best computer science and software engineering resources, built by and for the developer community." />
            <meta head-key="about:og:description" property="og:description" content="Learn about ComputerScienceResources.com — our mission to organize the best computer science and software engineering resources, built by and for the developer community." />
            <meta head-key="about:og:type" property="og:type" content="website" />
            <meta head-key="about:og:image" property="og:image" :content="defaultOgImage()" />
            <link head-key="about:canonical" rel="canonical" :href="canonicalFor('/about')" />
            <meta head-key="about:og:url" property="og:url" :content="canonicalFor('/about')" />
            <meta head-key="about:og:site_name" property="og:site_name" :content="SITE_NAME" />
        </Head>
            <main class="max-w-4xl mx-auto sm:px-6 lg:px-8 py-7">
                <!-- Header -->
                <div class="mb-8">
                    <h1 class="text-4xl font-bold mb-3 text-balance text-primary">About Computer Science Resources.com</h1>
                    <p class="text-lg text-gray-600 dark:text-gray-400 text-pretty">
                        Learn about our mission, origin, and purpose!
                    </p>
                </div>
                <div
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg"
                >
                    <div class="p-6 sm:p-12">

                        <!-- Main Content -->
                        <div
                            class="prose prose-lg max-w-none text-gray-700 dark:text-gray-300 space-y-6"
                        >
                            <p>
                                Hi, I'm Allan Kong, the creator of ComputerScienceResources.com.
                            </p>
                            <p>
                                Over the years, I've noticed that the best resources for learning computer science and software engineering are scattered all over the internet—hidden in newsletters, YouTube playlists, GitHub repositories, and outdated blog posts. I had enough people ask me, "What do you use to learn X in computer science?" that I realized there needed to be an entire web app dedicated to organizing and sharing these resources.
                            </p>
                            <p>
                                Software is a unique industry—most of us enter it as hobbyists, tinker with side projects, and are constantly learning. Computer science is a vast field, with so much to explore: data science, algorithms, databases, infrastructure, and more.
                            </p>
                            <ApplicationLogo class="mx-auto" />
                            <p>
                                My goal with ComputerScienceResources.com is to make learning easier by organizing and highlighting the best resources in computer science and software engineering. I want to help the developer community thrive by providing a central place to discover, review, and share valuable content.
                            </p>
                            <div
                                class="bg-secondary dark:bg-gray-700 rounded-lg p-6"
                            >
                                <p
                                    class="text-primary dark:text-accent font-semibold"
                                >
                                    My Mission
                                </p>
                                <p class="text-gray-800 dark:text-gray-200">
                                    I built this site because I believe learning should be accessible and organized. I want to give back to the community by making it easier for everyone to find high-quality resources, no matter where they are in their journey.
                                </p>
                            </div>
                            <p>
                                Anyone can post resources, review them, and suggest edits to any existing resource description. I want this to be a community-driven project where everyone can contribute and benefit.
                            </p>
                            <p>
                                On the technical side, the GitHub repository will be open to discussion and feedback soon! Don't hesitate to reach out there if you have ideas or want to help improve the site.
                            </p>
                            <p
                                class="text-center text-lg font-medium text-primary dark:text-accent mt-8"
                            >
                                <b>ComputerScienceResources.com values the community—because in the end, we all love software!</b>
                            </p>
                        </div>

                        <!-- FAQ -->
                        <div class="mt-6 p-4">
                            <h2 class="font-bold text-center my-5 text-lg">
                                Frequently Asked Questions
                            </h2>
                            <FrequentlyAskedQuestion>
                                <template #question>
                                    Who is this site for?
                                </template>
                                <template #answer>
                                    Anyone who wants to learn computer science! I include resources for everyone—seniors, children, beginners, and experts. This site is for anyone who wants to grow their knowledge, no matter their background.
                                </template>
                            </FrequentlyAskedQuestion>

                            <FrequentlyAskedQuestion>
                                <template #question>
                                    Why is Computer Science Resources not open source if you love the community?
                                </template>
                                <template #answer>
                                    It will be! I wanted to keep it closed source in the early release to prevent people from forking the codebase and taking credit for the project. I also need to do some refactoring to make it more appealing for contributors. I'll be happy to open source it in the near future—ideally once there are at least 200 users.
                                </template>
                            </FrequentlyAskedQuestion>

                            <FrequentlyAskedQuestion>
                                <template #question>
                                    How does Computer Science Resources make money?
                                </template>
                                <template #answer>
                                    I don't plan to lock anything behind a paywall. If I ever monetize, it will be through donations or non-intrusive ads—never at the expense of accessibility.
                                </template>
                            </FrequentlyAskedQuestion>

                            <FrequentlyAskedQuestion>
                                <template #question>
                                    What kind of resources are accepted?
                                </template>
                                <template #answer>
                                    We focus on comprehensive, structured, and high-quality resources—things that provide long-term learning value. That includes interactive platforms, educational YouTube channels, curated book series, technical newsletters, and more. The more focused or specialized the resource is, the better.
                                </template>
                            </FrequentlyAskedQuestion>

                            <FrequentlyAskedQuestion>
                                <template #question>
                                    Can I post my own course?
                                </template>
                                <template #answer>
                                    Only if it's well-received and genuinely useful. This isn't a place to advertise low-quality content—I'll take those down. I care more about substance than self-promotion. If your course is loved by the community, it's welcome here.
                                </template>
                            </FrequentlyAskedQuestion>

                            <FrequentlyAskedQuestion>
                                <template #question>
                                    Is the site finished?
                                </template>
                                <template #answer>
                                    Of course not! I'm still developing new features and improving the site. If I waited for perfection, it would never launch. I'm always listening to community feedback and working to make things better. Feel free to point out issues in the GitHub repository or discussions tab! Some things in the works: user profiles, reporting malicious posts and users, and favoriting resources.
                                </template>
                            </FrequentlyAskedQuestion>

                            <FrequentlyAskedQuestion>
                                <template #question>
                                    Why is the Logo a Cat?
                                </template>
                                <template #answer>
                                    I love cats. What else can I say? I have two cats and I simply love them. I think the world is better with cats involved.
                                </template>
                            </FrequentlyAskedQuestion>
                        </div>
                    </div>
                </div>
            </main>
    </AppLayout>
</template>
