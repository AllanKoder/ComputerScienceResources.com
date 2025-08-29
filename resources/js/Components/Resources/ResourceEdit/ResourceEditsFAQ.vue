<script setup>
import FrequentlyAskedQuestion from "@/Components/FrequentlyAskedQuestion.vue";
</script>

<template>
    <div class="space-y-6">
        <FrequentlyAskedQuestion>
            <template #question> What are Proposed Edits? </template>
            <template #answer>
                <p>
                    People post mistakes, or resources can grow outdated. This
                    is why we thought it was vital that people can suggest
                    changes to existing resources. If you see that something
                    doesn't add up on a resource page, or that you want to
                    improve it, feel free to create a proposed edit. The
                    community will vote on the edits and given enough approvals,
                    the changes will be merged in!
                </p>
            </template>
        </FrequentlyAskedQuestion>

        <FrequentlyAskedQuestion>
            <template #question> How does the voting system work? </template>
            <template #answer>
                <div class="space-y-4">
                    <p>
                        The number of votes required for a proposed edit to be
                        approved depends on how popular the resource is. We use
                        a logarithmic formula to ensure that highly popular
                        resources need more votes, but not an overwhelming
                        amount.
                    </p>

                    <div>
                        <h4 class="font-semibold mb-2">Formula</h4>
                        <pre
                            class="bg-gray-100 dark:bg-gray-800 p-3 rounded text-sm overflow-x-auto"
                        ><code>public function requiredVotes(int $totalVotes): int
{
    if ($totalVotes == 0) return 1;

    // Take the minimum of total votes OR the logarithmic calculation
    $votes = min($totalVotes, floor(log($totalVotes, 1.25)) + 1);

    // Ensure minimum of 3 votes is always required
    return max(3, $votes);
}</code>
            </pre>
                    </div>

                    <div>
                        <h4 class="font-semibold mb-2">How It Works</h4>
                        <ol class="list-decimal list-inside space-y-1">
                            <li>
                                <strong>For resources with few votes:</strong>
                                The required approvals equals the total votes on
                                the resource
                            </li>
                            <li>
                                <strong>For popular resources:</strong> We use a
                                logarithmic scale (base 1.25) to prevent
                                approval requirements from becoming unreasonably
                                high
                            </li>
                            <li>
                                <strong>Minimum threshold:</strong> All proposed
                                edits require at least 3 approvals, regardless
                                of resource popularity
                            </li>
                            <li>
                                <strong>No Votes:</strong> A single approval
                                is all you need to get your change in. If you post a resource,
                                don't forget to upvote it! Otherwise, someone could make an edit!!!
                            </li>
                        </ol>
                    </div>
                    <div class="overflow-x-auto">
                        <table
                            class="w-full border-collapse border border-gray-300 dark:border-gray-600"
                        >
                            <thead>
                                <tr class="bg-gray-100 dark:bg-gray-700">
                                    <th
                                        class="border border-gray-300 dark:border-gray-600 px-4 py-2 text-left"
                                    >
                                        Total Votes on Resource
                                    </th>
                                    <th
                                        class="border border-gray-300 dark:border-gray-600 px-4 py-2 text-left"
                                    >
                                        Required Approvals
                                    </th>
                                    <th
                                        class="border border-gray-300 dark:border-gray-600 px-4 py-2 text-left"
                                    >
                                        Explanation
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td
                                        class="border border-gray-300 dark:border-gray-600 px-4 py-2"
                                    >
                                        0
                                    </td>
                                    <td
                                        class="border border-gray-300 dark:border-gray-600 px-4 py-2"
                                    >
                                        1
                                    </td>
                                    <td
                                        class="border border-gray-300 dark:border-gray-600 px-4 py-2"
                                    >
                                        Since there are no existing votes (positive or negative), the entire community can merge any edits.<th></th>
                                        The reasoning is that someone could've made a typo and needed a quick fix.<th></th>
                                        Everyone automatically upvotes their own resources. So, you must unvote your newly created resource to reach 0 votes.
                                    </td>
                                </tr>
                                <tr>
                                    <td
                                        class="border border-gray-300 dark:border-gray-600 px-4 py-2"
                                    >
                                        1
                                    </td>
                                    <td
                                        class="border border-gray-300 dark:border-gray-600 px-4 py-2"
                                    >
                                        3
                                    </td>
                                    <td
                                        class="border border-gray-300 dark:border-gray-600 px-4 py-2"
                                    >
                                        Minimum requirement applies
                                    </td>
                                </tr>
                                <tr>
                                    <td
                                        class="border border-gray-300 dark:border-gray-600 px-4 py-2"
                                    >
                                        5
                                    </td>
                                    <td
                                        class="border border-gray-300 dark:border-gray-600 px-4 py-2"
                                    >
                                        5
                                    </td>
                                    <td
                                        class="border border-gray-300 dark:border-gray-600 px-4 py-2"
                                    >
                                        Equals total votes (below logarithmic
                                        threshold)
                                    </td>
                                </tr>
                                <tr>
                                    <td
                                        class="border border-gray-300 dark:border-gray-600 px-4 py-2"
                                    >
                                        25
                                    </td>
                                    <td
                                        class="border border-gray-300 dark:border-gray-600 px-4 py-2"
                                    >
                                        15
                                    </td>
                                    <td
                                        class="border border-gray-300 dark:border-gray-600 px-4 py-2"
                                    >
                                        log₁.₂₅(25) + 1 = 14 + 1 = 15
                                    </td>
                                </tr>
                                <tr>
                                    <td
                                        class="border border-gray-300 dark:border-gray-600 px-4 py-2"
                                    >
                                        1,000
                                    </td>
                                    <td
                                        class="border border-gray-300 dark:border-gray-600 px-4 py-2"
                                    >
                                        31
                                    </td>
                                    <td
                                        class="border border-gray-300 dark:border-gray-600 px-4 py-2"
                                    >
                                        log₁.₂₅(1,000) + 1 = 30 + 1 = 31
                                    </td>
                                </tr>
                                <tr>
                                    <td
                                        class="border border-gray-300 dark:border-gray-600 px-4 py-2"
                                    >
                                        1,000,000
                                    </td>
                                    <td
                                        class="border border-gray-300 dark:border-gray-600 px-4 py-2"
                                    >
                                        62
                                    </td>
                                    <td
                                        class="border border-gray-300 dark:border-gray-600 px-4 py-2"
                                    >
                                        log₁.₂₅(1,000,000) + 1 = 61 + 1 = 62
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div>
                        <h4 class="font-semibold mb-2">Why This System?</h4>
                        <ul class="space-y-1">
                            <li>
                                <strong>Fairness:</strong> New resources with
                                few votes don't need excessive approvals
                            </li>
                            <li>
                                <strong>Scalability:</strong> Popular resources
                                with thousands of votes don't require thousands
                                of approvals
                            </li>
                            <li>
                                <strong>Quality Control:</strong> The
                                logarithmic scale ensures that changes to
                                popular resources still require substantial
                                community consensus
                            </li>
                            <li>
                                <strong>Accessibility:</strong> The minimum of 3
                                votes ensures that even unpopular resources can
                                be improved by a small group of engaged users
                            </li>
                        </ul>
                    </div>

                    <div>
                        <h4 class="font-semibold mb-2">Will this system change?</h4>
                        <p>Yes, this is how I set the voting system for the initial release. But it will change depending on the community's feedback.</p>
                    </div>
                </div>
            </template>
        </FrequentlyAskedQuestion>
    </div>
</template>
