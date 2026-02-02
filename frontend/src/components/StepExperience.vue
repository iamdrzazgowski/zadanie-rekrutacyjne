<template>
    <div class="experience-wrapper">
        <div
            v-for="(exp, index) in model"
            :key="index"
            class="experience-block"
        >
            <div class="experience-header">
                <h4>Doświadczenie {{ index + 1 }}</h4>
                <button
                    type="button"
                    @click="remove(index)"
                    class="btn-remove"
                    title="Usuń to doświadczenie"
                >
                    <span class="icon-close">✕</span>
                </button>
            </div>

            <div class="form-group">
                <label>Firma:</label>
                <input
                    type="text"
                    :value="exp.company"
                    @input="update(index, 'company', $event.target.value)"
                    :class="{ 'has-error': errors[index]?.company }"
                    placeholder="Nazwa firmy"
                />
                <span class="error">{{ errors[index]?.company }}</span>
            </div>

            <div class="form-group">
                <label>Stanowisko:</label>
                <input
                    type="text"
                    :value="exp.position"
                    @input="update(index, 'position', $event.target.value)"
                    :class="{ 'has-error': errors[index]?.position }"
                    placeholder="Twoje stanowisko"
                />
                <span class="error">{{ errors[index]?.position }}</span>
            </div>

            <div class="date-row">
                <div class="form-group">
                    <label>Data od:</label>
                    <input
                        type="date"
                        :value="exp.dateFrom"
                        @input="update(index, 'dateFrom', $event.target.value)"
                        :class="{ 'has-error': errors[index]?.dateFrom }"
                    />
                    <span class="error">{{ errors[index]?.dateFrom }}</span>
                </div>

                <div class="form-group">
                    <label>Data do:</label>
                    <input
                        type="date"
                        :value="exp.dateTo"
                        @input="update(index, 'dateTo', $event.target.value)"
                        :class="{ 'has-error': errors[index]?.dateTo }"
                    />
                    <span class="error">{{ errors[index]?.dateTo }}</span>
                </div>
            </div>
        </div>

        <button type="button" @click="add" class="btn-add">
            <span class="icon-plus">+</span>
            Dodaj doświadczenie
        </button>
    </div>
</template>

<script setup>
const props = defineProps({
    model: { type: Array, required: true },
    errors: { type: Array, required: true },
});
const emit = defineEmits(["update:model"]);
function update(index, field, value) {
    const newArr = [...props.model];
    newArr[index] = { ...newArr[index], [field]: value };
    emit("update:model", newArr);
}
function add() {
    emit("update:model", [
        ...props.model,
        { company: "", position: "", dateFrom: "", dateTo: "" },
    ]);
}
function remove(index) {
    const newArr = [...props.model];
    newArr.splice(index, 1);
    emit("update:model", newArr);
}
</script>

<style scoped>
.experience-wrapper {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.experience-block {
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    border: 2px solid #e0e6ed;
    border-radius: 15px;
    padding: 25px;
    position: relative;
    transition: all 0.3s ease;
    animation: slideIn 0.3s ease;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.experience-block:hover {
    border-color: #667eea;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.15);
}

.experience-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid #e0e6ed;
}

.experience-header h4 {
    margin: 0;
    color: #667eea;
    font-size: 1.2rem;
    font-weight: 600;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-bottom: 16px;
}

.date-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}

label {
    display: block;
    margin: 0;
    font-weight: 600;
    color: #2c3e50;
    font-size: 0.95rem;
    letter-spacing: 0.3px;
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
    background: #ffffff;
    box-sizing: border-box;
}

input:focus {
    outline: none;
    border-color: #667eea;
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

/* Przyciski */
.btn-remove {
    background: transparent;
    border: 2px solid #e74c3c;
    color: #e74c3c;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    padding: 0;
    flex-shrink: 0;
}

.btn-remove:hover {
    background: #e74c3c;
    color: white;
    transform: rotate(90deg) scale(1.1);
    box-shadow: 0 4px 12px rgba(231, 76, 60, 0.3);
}

.icon-close {
    font-size: 1.2rem;
    font-weight: bold;
    line-height: 1;
}

.btn-add {
    background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
    border: none;
    color: white;
    padding: 16px 32px;
    border-radius: 12px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    box-shadow: 0 4px 15px rgba(46, 204, 113, 0.3);
    margin-top: 10px;
}

.btn-add:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(46, 204, 113, 0.4);
}

.btn-add:active {
    transform: translateY(0);
}

.icon-plus {
    font-size: 1.5rem;
    font-weight: bold;
    line-height: 1;
}
</style>
