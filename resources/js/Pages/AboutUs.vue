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
        <div class="py-7">
            <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg"
                >
                    <div class="p-6 sm:p-12">
                        <!-- Header -->
                        <div class="text-center mb-12">
                            <h1 class="text-4xl font-bold text-primary">
                                About Computer Science Resources.com
                            </h1>
                        </div>

                        <!-- Main Content -->
                        <div
                            class="prose prose-lg max-w-none text-gray-700 dark:text-gray-300 space-y-6"
                        >
                            <p>
                                Software is a unique industry — most people
                                enter it as hobbyists, tinker with side
                                projects, and are constantly learning.
                            </p>

                            <p>
                                Computer science is a wide field. There is a lot
                                to learn: from data science, algorithms,
                                databases and infrastructure.
                            </p>

                            <ApplicationLogo class="mx-auto" />

                            <p>
                                On the plus side, there are a ton of resources
                                that are out there. The problem? It is scattered
                                all over the internet - buried in newsletters,
                                YouTube playlists, GitHub repositories, and
                                outdated blog posts.
                            </p>

                            <p>
                                It's often difficult to know what a resource
                                actually teaches, who is the intended audience,
                                or how well it's received by the community.
                            </p>

                            <div
                                class="bg-secondary dark:bg-gray-700 rounded-lg p-6"
                            >
                                <p
                                    class="text-primary dark:text-accent font-semibold"
                                >
                                    Our Mission
                                </p>
                                <p class="text-gray-800 dark:text-gray-200">
                                    Our goal with ComputerScienceResources.com
                                    is to make learning easier by organizing and
                                    highlighting the best resources in computer
                                    science and software engineering. We aim to
                                    help the developer community thrive by
                                    providing a central place to discover,
                                    review, and share valuable content.
                                </p>
                            </div>

                            <p>
                                In addition, we provide the community the
                                opportunity to give back to the website.
                                Everyone can post resources, review resources,
                                and suggest edits to any existing resource
                                description.
                            </p>

                            <p>
                                On the more technical side, our github will be
                                open to discussion and feedback soon! Don't be
                                afraid to reach out there!
                            </p>

                            <p
                                class="text-center text-lg font-medium text-primary dark:text-accent mt-8"
                            >
                                ComputerScienceResources.com values the
                                community, because in the end, we all love
                                software!
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
                                    Everyone! Everyone can all learn computer
                                    science. We include resources for seniors
                                    and children. This site is for everyone.
                                </template>
                            </FrequentlyAskedQuestion>

                            <FrequentlyAskedQuestion>
                                <template #question>
                                    Why is Computer Science Resources not open
                                    source if you love the community?
                                </template>
                                <template #answer>
                                    It will be :). We wanted to keep it closed
                                    source in the early release to prevent
                                    people from forking the codebase and
                                    stealing the project's credit. Furthermore,
                                    we need to do some refactoring to make it
                                    more appealing to those who wish to
                                    contribute to the codebase. We will be happy
                                    to open source it in the near future with
                                    ideally 200 users minimum.
                                </template>
                            </FrequentlyAskedQuestion>

                            <FrequentlyAskedQuestion>
                                <template #question>
                                    How does Computer Science Resources make
                                    money?
                                </template>
                                <template #answer>
                                    We don't plan to make anything locked behind
                                    a paywall anytime in the future. The plans
                                    for monetization will be from donations, or
                                    non-intrusive ads in the future.
                                </template>
                            </FrequentlyAskedQuestion>

                            <FrequentlyAskedQuestion>
                                <template #question>
                                    What kind of resources are accepted?
                                </template>
                                <template #answer>
                                    We focus on comprehensive, structured, and
                                    high-quality resources — things that provide
                                    long-term learning value. That includes
                                    interactive platforms, educational YouTube
                                    channels, curated book series, technical
                                    newsletters, and more. The more focused or
                                    specialized the resource is, the better.
                                </template>
                            </FrequentlyAskedQuestion>

                            <FrequentlyAskedQuestion>
                                <template #question>
                                    Can I post my own course?
                                </template>
                                <template #answer>
                                    Only if it's well-received and genuinely
                                    useful. This isn't a place to advertise
                                    low-quality content, we will take those
                                    down. We care more about substance than
                                    self-promotion. If your course is loved by
                                    the community, it's welcome.
                                </template>
                            </FrequentlyAskedQuestion>

                            <FrequentlyAskedQuestion>
                                <template #question>
                                    Is the site finished?
                                </template>
                                <template #answer>
                                    Of course not, we are still developing
                                    things. There are a lot of features that
                                    have not been implemented yet for the sake
                                    of developing a release. If I were to make
                                    the first release perfect, it wouldn't be
                                    out here. So, we will be constantly
                                    listening to community feedback and
                                    improving the app. Feel free to point out
                                    issues in the github repository tool and
                                    github discussions tab! Some things that are
                                    in the works are the ability to view
                                    someone's profile, report malicious posts
                                    and users, and favoriting a resource.
                                </template>
                            </FrequentlyAskedQuestion>

                            <FrequentlyAskedQuestion>
                                <template #question>
                                    Why is the Logo a Cat?
                                </template>
                                <template #answer>
                                    I love cats. What else can I say? I have two
                                    cats and I simply love them. I think the
                                    world is better with cats involved.
                                </template>
                            </FrequentlyAskedQuestion>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
