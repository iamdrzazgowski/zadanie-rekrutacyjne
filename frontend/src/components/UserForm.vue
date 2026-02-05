<template>
    <div class="form-card">
        <!-- Progress Indicator -->
        <div class="step-indicator">
            <div
                v-for="n in 3"
                :key="n"
                class="step-item"
                :class="{ active: n === step, completed: n < step }"
            >
                <div class="step-circle">
                    <span v-if="n < step" class="checkmark">✓</span>
                    <span v-else>{{ n }}</span>
                </div>
                <div class="step-label">
                    {{ getStepLabel(n) }}
                </div>
                <div
                    v-if="n < 3"
                    class="step-line"
                    :class="{ completed: n < step }"
                ></div>
            </div>
        </div>

        <h2 class="step-title">{{ getStepTitle(step) }}</h2>

        <!-- Krok formularza -->
        <transition name="slide-fade" mode="out-in">
            <StepBasic
                v-if="step === 1"
                key="basic"
                v-model:model="form.basic"
                :errors="errors.basic"
            />
            <StepContact
                v-else-if="step === 2"
                key="contact"
                v-model:model="form.contact"
                :errors="errors.contact"
            />
            <StepExperience
                v-else-if="step === 3"
                key="experience"
                v-model:model="form.experience"
                :errors="errors.experience"
            />
        </transition>

        <!-- Nawigacja -->
        <div class="navigation">
            <button
                @click="prevStep"
                :disabled="step === 1"
                class="btn-secondary"
            >
                <span class="btn-icon">←</span>
                Wstecz
            </button>
            <button @click="nextStep" v-if="step < 3" class="btn-primary">
                Dalej
                <span class="btn-icon">→</span>
            </button>
            <button
                @click="submitForm"
                v-if="step === 3"
                :disabled="loading"
                class="btn-submit"
            >
                <span v-if="!loading">Wyślij</span>
                <span v-else class="spinner">
                    <span class="spinner-icon">⟳</span>
                    Wysyłanie...
                </span>
            </button>
        </div>

        <!-- Komunikaty -->
        <transition name="fade">
            <div v-if="successMessage" class="alert success">
                <div class="alert-icon">✓</div>
                <div class="alert-content">
                    <strong>Sukces!</strong>
                    <p>{{ successMessage }}</p>
                </div>
            </div>
        </transition>

        <transition name="fade">
            <div v-if="backendErrors.length" class="alert error">
                <div class="alert-icon">⚠</div>
                <div class="alert-content">
                    <strong>Wystąpiły błędy:</strong>
                    <ul>
                        <li v-for="(err, idx) in backendErrors" :key="idx">
                            {{ err }}
                        </li>
                    </ul>
                </div>
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

function getStepLabel(stepNum) {
    const labels = {
        1: "Dane osobowe",
        2: "Kontakt",
        3: "Doświadczenie",
    };
    return labels[stepNum];
}

function getStepTitle(stepNum) {
    const titles = {
        1: "Podstawowe informacje",
        2: "Dane kontaktowe",
        3: "Twoje doświadczenie",
    };
    return titles[stepNum];
}

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
    max-width: 800px;
    margin: 0 auto;
}

/* Step Indicator */
.step-indicator {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 50px;
    position: relative;
}

.step-item {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
}

.step-circle {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: #e0e7ff;
    border: 3px solid #e0e7ff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 1.1rem;
    color: #94a3b8;
    transition: all 0.3s ease;
    position: relative;
    z-index: 2;
}

.step-item.active .step-circle {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-color: #667eea;
    color: white;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    transform: scale(1.1);
}

.step-item.completed .step-circle {
    background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
    border-color: #2ecc71;
    color: white;
    box-shadow: 0 4px 12px rgba(46, 204, 113, 0.3);
}

.checkmark {
    font-size: 1.4rem;
    animation: checkPop 0.3s ease;
}

@keyframes checkPop {
    0% {
        transform: scale(0);
    }
    50% {
        transform: scale(1.2);
    }
    100% {
        transform: scale(1);
    }
}

.step-label {
    margin-top: 12px;
    font-size: 0.9rem;
    font-weight: 600;
    color: #94a3b8;
    text-align: center;
    transition: color 0.3s ease;
}

.step-item.active .step-label {
    color: #667eea;
}

.step-item.completed .step-label {
    color: #2ecc71;
}

.step-line {
    position: absolute;
    top: 25px;
    left: 50%;
    width: 100%;
    height: 3px;
    background: #e0e7ff;
    z-index: 1;
    transition: background 0.3s ease;
}

.step-line.completed {
    background: linear-gradient(90deg, #2ecc71 0%, #27ae60 100%);
}

.step-item:last-child .step-line {
    display: none;
}

/* Title */
.step-title {
    color: #1e293b;
    margin: 0 0 35px 0;
    font-size: 1.8rem;
    font-weight: 700;
    text-align: center;
    letter-spacing: -0.5px;
}

/* Navigation */
.navigation {
    margin-top: 35px;
    display: flex;
    gap: 15px;
    justify-content: center;
}

button {
    padding: 14px 36px;
    border-radius: 12px;
    border: none;
    cursor: pointer;
    font-size: 1rem;
    font-weight: 600;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    gap: 8px;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
}

.btn-secondary {
    background: white;
    color: #667eea;
    border: 2px solid #667eea;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.btn-secondary:hover {
    background: #f0f4ff;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
}

.btn-submit {
    background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(46, 204, 113, 0.4);
}

.btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(46, 204, 113, 0.5);
}

button:active {
    transform: translateY(0);
}

button:disabled {
    background: #e2e8f0;
    color: #94a3b8;
    cursor: not-allowed;
    box-shadow: none;
    transform: none;
    border: none;
}

.btn-icon {
    font-size: 1.2rem;
    transition: transform 0.3s ease;
}

button:hover .btn-icon {
    transform: translateX(3px);
}

.btn-secondary:hover .btn-icon {
    transform: translateX(-3px);
}

.spinner {
    display: flex;
    align-items: center;
    gap: 8px;
}

.spinner-icon {
    display: inline-block;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

/* Alerts */
.alert {
    margin-top: 25px;
    padding: 20px;
    border-radius: 12px;
    display: flex;
    gap: 15px;
    align-items: flex-start;
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

.alert-icon {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3rem;
    font-weight: bold;
    flex-shrink: 0;
}

.alert-content {
    flex: 1;
}

.alert-content strong {
    display: block;
    margin-bottom: 8px;
    font-size: 1.1rem;
}

.alert-content p {
    margin: 0;
}

.alert.success {
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
    color: #155724;
    border-left: 4px solid #28a745;
}

.alert.success .alert-icon {
    background: #28a745;
    color: white;
}

.alert.error {
    background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
    color: #721c24;
    border-left: 4px solid #dc3545;
}

.alert.error .alert-icon {
    background: #dc3545;
    color: white;
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

/* Transitions */
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

.slide-fade-enter-active {
    transition: all 0.3s ease-out;
}

.slide-fade-leave-active {
    transition: all 0.3s ease-in;
}

.slide-fade-enter-from {
    transform: translateX(20px);
    opacity: 0;
}

.slide-fade-leave-to {
    transform: translateX(-20px);
    opacity: 0;
}

/* Responsive */
@media (max-width: 768px) {
    .form-card {
        padding: 25px;
    }

    .step-indicator {
        margin-bottom: 35px;
    }

    .step-circle {
        width: 40px;
        height: 40px;
        font-size: 1rem;
    }

    .step-label {
        font-size: 0.75rem;
        margin-top: 8px;
    }

    .step-line {
        top: 20px;
    }

    .step-title {
        font-size: 1.4rem;
        margin-bottom: 25px;
    }

    .navigation {
        flex-direction: column;
    }

    button {
        width: 100%;
        justify-content: center;
    }
}
</style>
