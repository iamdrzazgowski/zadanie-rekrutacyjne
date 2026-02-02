<template>
    <div>
        <div class="form-group">
            <label>Telefon:</label>
            <input
                type="tel"
                :value="model.phone"
                @input="update('phone', $event.target.value)"
                :class="{ 'has-error': errors.phone }"
                placeholder="+48 123 456 789"
            />
            <span class="error">{{ errors.phone }}</span>
        </div>

        <div class="form-group">
            <label>Email:</label>
            <input
                type="email"
                :value="model.email"
                @input="update('email', $event.target.value)"
                :class="{ 'has-error': errors.email }"
                placeholder="twoj@email.com"
            />
            <span class="error">{{ errors.email }}</span>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    model: { type: Object, required: true },
    errors: { type: Object, required: true },
});
const emit = defineEmits(["update:model"]);
function update(field, value) {
    emit("update:model", { ...props.model, [field]: value });
}
</script>

<style scoped>
div {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

label {
    display: block;
    margin: 0;
    font-weight: 600;
    color: #2c3e50;
    font-size: 0.95rem;
    letter-spacing: 0.3px;
    transition: color 0.3s ease;
}

input {
    width: 100%;
    padding: 14px 16px;
    margin: 0;
    border: 2px solid #e0e6ed;
    border-radius: 10px;
    font-size: 1rem;
    font-family: inherit;
    transition: all 0.3s ease;
    background: #f8f9fa;
    box-sizing: border-box;
}

input:focus {
    outline: none;
    border-color: #667eea;
    background: #ffffff;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
    transform: translateY(-1px);
}

input:hover:not(:focus) {
    border-color: #b8c2d1;
}

input.has-error {
    border-color: #e74c3c;
    background: #fff5f5;
}

input.has-error:focus {
    border-color: #e74c3c;
    box-shadow: 0 0 0 4px rgba(231, 76, 60, 0.1);
}

.error {
    color: #e74c3c;
    font-size: 0.875rem;
    font-weight: 500;
    min-height: 20px;
    display: flex;
    align-items: center;
    gap: 6px;
    margin-top: -4px;
    animation: shake 0.3s ease;
}

.error:not(:empty)::before {
    content: "⚠";
    font-size: 0.9rem;
}

@keyframes shake {
    0%,
    100% {
        transform: translateX(0);
    }
    25% {
        transform: translateX(-5px);
    }
    75% {
        transform: translateX(5px);
    }
}
</style>
