<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";
import { ref, onMounted, computed } from "vue";

// Define props
const props = defineProps({
    teams: {
        type: Array,
        default: () => [],
    },
});

// Initialize the form
const applyForm = useForm({
    team_id: null,
    tournament_id: null,
});

// Reactive tournaments data
const tournaments = ref([]);

// Loading state for form submission
const isSubmitting = ref(false);

// Check if the user is part of any team
const userTeams = computed(() => {
    const userId = $page.props.auth.user?.id;
    return props.teams.filter((team) =>
        team.users.some((u) => u.id === userId)
    );
});

// Fetch tournaments on mount
onMounted(() => {
    fetchTournaments();
});

// Fetch tournaments function
const fetchTournaments = () => {
    console.log("Fetching tournaments...");
    $inertia.get(
        route("tournaments"),
        { for_teams: true },
        {
            onSuccess: (response) => {
                tournaments.value = Array.isArray(response)
                    ? response
                    : response.props.tournaments || [];
                console.log("Tournaments loaded:", tournaments.value);
            },
            onError: (errors) => {
                console.log("Error loading tournaments:", errors);
                tournaments.value = [];
            },
        }
    );
};

// Submit application function
const submitApplication = () => {
    if (!applyForm.team_id || !applyForm.tournament_id) {
        console.error("Team or tournament not selected");
        applyForm.setError("team_id", "Пожалуйста, выберите команду.");
        applyForm.setError("tournament_id", "Пожалуйста, выберите турнир.");
        return;
    }

    console.log(
        "Submitting application for team:",
        applyForm.team_id,
        "Tournament:",
        applyForm.tournament_id
    );

    isSubmitting.value = true;
    applyForm
        .post(route("dashboard.teams.submit-application"), {
            onSuccess: () => {
                console.log("Application submitted successfully");
                applyForm.reset();
                isSubmitting.value = false;
            },
            onError: (errors) => {
                console.log("Error submitting application:", errors);
                applyForm.setError(errors);
                isSubmitting.value = false;
            },
            onFinish: () => {
                console.log("Request finished");
            },
        })
        .catch((error) => {
            console.error("Unexpected error during submission:", error);
            isSubmitting.value = false;
        });
};
</script>

<template>
    <Head title="Мои команды - Drop Cyber Lounge" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Мои команды
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            Мои команды
                        </h3>
                        <div v-if="userTeams.length" class="space-y-4">
                            <div
                                v-for="team in userTeams"
                                :key="team.id"
                                class="border-b border-gray-200 pb-4"
                            >
                                <h4 class="text-md font-medium text-gray-700">
                                    {{ team.name }}
                                </h4>
                                <p class="text-sm text-gray-600">
                                    Капитан:
                                    {{ team.captain?.name || "Не указан" }}
                                </p>
                                <p class="text-sm text-gray-600">
                                    Участники:
                                    {{
                                        team.users.map((u) => u.name).join(", ")
                                    }}
                                </p>
                                <p class="text-sm text-gray-600">
                                    Код приглашения:
                                    {{
                                        route("dashboard.teams.join", {
                                            invite_code: team.invite_code,
                                        })
                                    }}
                                </p>
                                <form
                                    @submit.prevent="submitApplication"
                                    class="mt-2 space-y-2"
                                >
                                    <div>
                                        <select
                                            v-model="applyForm.team_id"
                                            :value="team.id"
                                            disabled
                                            class="w-full p-2 border rounded mb-2"
                                        >
                                            <option :value="team.id">
                                                {{ team.name }}
                                            </option>
                                        </select>
                                        <select
                                            v-model="applyForm.tournament_id"
                                            required
                                            class="w-full p-2 border rounded"
                                            :disabled="isSubmitting"
                                        >
                                            <option disabled value="">
                                                Выберите турнир
                                            </option>
                                            <option
                                                v-for="tournament in tournaments"
                                                :key="tournament.id"
                                                :value="tournament.id"
                                            >
                                                {{ tournament.name }}
                                            </option>
                                        </select>
                                        <div
                                            v-if="!tournaments.length"
                                            class="text-red-500 text-sm mt-2"
                                        >
                                            Нет доступных турниров для команд.
                                        </div>
                                    </div>
                                    <button
                                        type="submit"
                                        :disabled="
                                            !applyForm.team_id ||
                                            !applyForm.tournament_id ||
                                            isSubmitting
                                        "
                                        class="rounded-md bg-blue-500 px-3 py-1 text-white hover:bg-blue-600"
                                    >
                                        {{
                                            isSubmitting
                                                ? "Подача..."
                                                : "Подать заявку"
                                        }}
                                    </button>
                                </form>
                                <div
                                    v-if="Object.keys(applyForm.errors).length"
                                    class="text-red-500 text-sm mt-2"
                                >
                                    <p
                                        v-for="(
                                            error, field
                                        ) in applyForm.errors"
                                        :key="field"
                                    >
                                        {{ error }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-sm text-gray-600">
                            У вас нет команд.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
