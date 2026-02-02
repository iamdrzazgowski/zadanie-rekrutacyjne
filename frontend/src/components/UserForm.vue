<template>
    <div class="form-card">
        <h2 class="step-title">Krok {{ step }} / 3</h2>

        <!-- Krok formularza -->
        <StepBasic
            v-if="step === 1"
            v-model:model="form.basic"
            :errors="errors.basic"
        />
        <StepContact
            v-if="step === 2"
            v-model:model="form.contact"
            :errors="errors.contact"
        />
        <StepExperience
            v-if="step === 3"
            v-model:model="form.experience"
            :errors="errors.experience"
        />

        <!-- Nawigacja -->
        <div class="navigation">
            <button @click="prevStep" :disabled="step === 1">Wstecz</button>
            <button @click="nextStep" v-if="step < 3">Dalej</button>
            <button @click="submitForm" v-if="step === 3" :disabled="loading">
                {{ loading ? "Wysyłanie..." : "Wyślij" }}
            </button>
        </div>

        <!-- Komunikaty -->
        <transition name="fade">
            <div v-if="successMessage" class="alert success">
                <strong>Sukces!</strong> {{ successMessage }}
            </div>
        </transition>

        <transition name="fade">
            <div v-if="backendErrors.length" class="alert error">
                <strong>Błędy:</strong>
                <ul>
                    <li v-for="(err, idx) in backendErrors" :key="idx">
                        {{ err }}
                    </li>
                </ul>
            </div>
        </transition>
    </div>
</template>

<script setup>
import { reactive, ref } from "vue";
import StepBasic from "./StepBasic.vue";
import StepContact from "./StepContact.vue";
import StepExperience from "./StepExperience.vue";

const step = ref(1);
const loading = ref(false);
const successMessage = ref("");
const backendErrors = ref([]);

const form = reactive({
    basic: { firstName: "", lastName: "", birthDate: "" },
    contact: { phone: "", email: "" },
    experience: [],
});

const errors = reactive({
    basic: {},
    contact: {},
    experience: [],
});

function clearErrors() {
    errors.basic = {};
    errors.contact = {};
    errors.experience = form.experience.map(() => ({}));
}

function validateStep() {
    clearErrors();
    let valid = true;

    if (step.value === 1) {
        if (!form.basic.firstName) {
            errors.basic.firstName = "Imię jest wymagane";
            valid = false;
        }
        if (!form.basic.lastName) {
            errors.basic.lastName = "Nazwisko jest wymagane";
            valid = false;
        }
        if (!form.basic.birthDate) {
            errors.basic.birthDate = "Data urodzenia jest wymagana";
            valid = false;
        } else if (new Date(form.basic.birthDate) >= new Date()) {
            errors.basic.birthDate = "Data urodzenia musi być w przeszłości";
            valid = false;
        }
    }

    if (step.value === 2) {
        const phoneRegex = /^\+?[0-9]{7,15}$/;
        if (!form.contact.phone) {
            errors.contact.phone = "Telefon jest wymagany";
            valid = false;
        } else if (!phoneRegex.test(form.contact.phone)) {
            errors.contact.phone = "Niepoprawny numer telefonu";
            valid = false;
        }

        if (!form.contact.email) {
            errors.contact.email = "Email jest wymagany";
            valid = false;
        } else if (!/\S+@\S+\.\S+/.test(form.contact.email)) {
            errors.contact.email = "Niepoprawny adres email";
            valid = false;
        }
    }

    if (step.value === 3) {
        form.experience.forEach((exp, index) => {
            errors.experience[index] = {};
            if (!exp.company) {
                errors.experience[index].company = "Firma jest wymagana";
                valid = false;
            }
            if (!exp.position) {
                errors.experience[index].position = "Stanowisko jest wymagane";
                valid = false;
            }
            if (!exp.dateFrom) {
                errors.experience[index].dateFrom = "Data od jest wymagana";
                valid = false;
            }
            if (!exp.dateTo) {
                errors.experience[index].dateTo = "Data do jest wymagana";
                valid = false;
            }
            if (
                exp.dateFrom &&
                exp.dateTo &&
                new Date(exp.dateFrom) > new Date(exp.dateTo)
            ) {
                errors.experience[index].dateFrom =
                    "Data od nie może być późniejsza niż data do";
                errors.experience[index].dateTo =
                    "Data do nie może być wcześniejsza niż data od";
                valid = false;
            }
        });
    }

    return valid;
}

function nextStep() {
    if (validateStep()) step.value++;
}
function prevStep() {
    step.value--;
}

async function submitForm() {
    if (!validateStep()) return;

    loading.value = true;
    successMessage.value = "";
    backendErrors.value = [];

    try {
        const response = await fetch("http://localhost:8080/api/user/submit", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(form),
        });

        const data = await response.json();

        if (!response.ok) {
            backendErrors.value = data.errors || ["Nieznany błąd"];
        } else {
            successMessage.value = data.message || "Dane zapisane pomyślnie!";
            // Reset formularza
            form.basic = { firstName: "", lastName: "", birthDate: "" };
            form.contact = { phone: "", email: "" };
            form.experience = [];
            step.value = 1;
        }
    } catch (err) {
        backendErrors.value = ["Błąd wysyłki."];
        console.error(err);
    } finally {
        loading.value = false;
    }
}
</script>

<style scoped>
.form-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    padding: 40px;
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.step-title {
    color: #667eea;
    margin: 0 0 30px 0;
    font-size: 1.5rem;
    font-weight: 600;
    text-align: center;
    letter-spacing: -0.3px;
}

.navigation {
    margin-top: 35px;
    display: flex;
    gap: 15px;
    justify-content: center;
}

button {
    padding: 12px 32px;
    border-radius: 12px;
    border: none;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    cursor: pointer;
    font-size: 1rem;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    position: relative;
    overflow: hidden;
}

button::before {
    content: "";
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(
        135deg,
        rgba(255, 255, 255, 0.2) 0%,
        rgba(255, 255, 255, 0) 100%
    );
    transition: left 0.3s ease;
}

button:hover::before {
    left: 100%;
}

button:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
}

button:active {
    transform: translateY(0);
}

button:disabled {
    background: linear-gradient(135deg, #bdc3c7 0%, #95a5a6 100%);
    cursor: not-allowed;
    box-shadow: none;
    transform: none;
}

button:disabled::before {
    display: none;
}

.alert {
    margin-top: 25px;
    padding: 18px 24px;
    border-radius: 12px;
    font-weight: 500;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    animation: slideIn 0.3s ease;
}

@keyframes slideIn {
    from {
        transform: translateY(-10px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.alert strong {
    display: block;
    margin-bottom: 8px;
    font-size: 1.1rem;
}

.alert.success {
    background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
    color: white;
    border-left: 4px solid #1e8449;
}

.alert.error {
    background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
    color: white;
    border-left: 4px solid #922b21;
}

.alert ul {
    margin: 8px 0 0 0;
    padding-left: 20px;
    list-style-type: none;
}

.alert ul li {
    position: relative;
    padding-left: 20px;
    margin-bottom: 5px;
}

.alert ul li::before {
    content: "→";
    position: absolute;
    left: 0;
    font-weight: bold;
}

.fade-enter-active,
.fade-leave-active {
    transition: all 0.4s ease;
}

.fade-enter-from {
    opacity: 0;
    transform: translateY(-20px);
}

.fade-leave-to {
    opacity: 0;
    transform: translateY(20px);
}
</style>
