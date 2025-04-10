<template>
    <div class="weekly-hours-bracket">
        <div class="hours-label" :class="{ 'text-red-600': hours !== '35h00' && hours !== '00h00' }">{{ hours || '00h00' }}</div>
        <div class="bracket-container">
            <div class="bracket-line"></div>
            <div class="bracket-left"></div>
            <div class="bracket-right"></div>
            <div class="week-display">Semaine {{ formattedWeekNumber }}</div>
        </div>
    </div>
</template>

<script>
export default {
    name: "WeeklyHoursBracket",
    props: {
        hours: {
            type: String,
            default: '00h00'
        },
        weekNumber: {
            type: [Number, String],
            required: true
        }
    },
    computed: {
        formattedWeekNumber() {
            // Convertir en chaîne de caractères si ce n'est pas déjà le cas
            const weekStr = String(this.weekNumber);

            // Si la longueur est de 6 caractères (comme 162025), on suppose que c'est semaine 16 de 2025
            if (weekStr.length === 6) {
                // Extraire les 2 premiers caractères pour la semaine et les 4 derniers pour l'année
                const week = weekStr.substring(0, 2);
                const year = weekStr.substring(2);
                return `${week}-${year}`;
            }

            // Si la longueur est de 5 caractères (comme 52024), on suppose que c'est semaine 5 de 2024
            if (weekStr.length === 5) {
                // Extraire le premier caractère pour la semaine et les 4 derniers pour l'année
                const week = weekStr.substring(0, 1);
                const year = weekStr.substring(1);
                return `${week}-${year}`;
            }

            // Sinon, retourner tel quel
            return weekStr;
        }
    }
}
</script>

<style scoped>
.weekly-hours-bracket {
    position: relative;
    width: 100%;
    margin: 0;
    padding: 0;
    height: 50px;
}

.hours-label {
    position: absolute;
    top: 0;
    left: 16px;
    background-color: white;
    padding: 4px 12px;
    border-radius: 16px;
    font-weight: bold;
    font-size: 0.95rem;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    border: 1px solid #E5E7EB;
    display: flex;
    align-items: center;
    z-index: 10;
}

.hours-label:before {
    content: '';
    display: inline-block;
    width: 16px;
    height: 16px;
    margin-right: 6px;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='currentColor'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z' /%3E%3C/svg%3E");
    background-size: contain;
    background-repeat: no-repeat;
}

.bracket-container {
    position: relative;
    width: 100%;
    height: 50px;
}

.bracket-line {
    position: absolute;
    bottom: 0;
    left: 2%;
    width: 96%;
    height: 3px;
    background-color: #4F46E5;
    border-radius: 3px;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}

.bracket-left {
    position: absolute;
    bottom: 0;
    left: 2%;
    width: 3px;
    height: 30px;
    background-color: #4F46E5;
    border-radius: 3px 3px 0 0;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}

.bracket-right {
    position: absolute;
    bottom: 0;
    right: 2%;
    width: 3px;
    height: 30px;
    background-color: #4F46E5;
    border-radius: 3px 3px 0 0;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}

.week-display {
    position: absolute;
    bottom: 15px;
    right: 16px;
    background-color: #4F46E5;
    color: white;
    padding: 4px 12px;
    border-radius: 16px;
    font-weight: 600;
    font-size: 0.85rem;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    z-index: 10;
}

.hours-label.text-red-600 {
    background-color: white;
    border-color: #FECACA;
}

.hours-icon {
    display: flex;
    align-items: center;
}

/* Animation */
@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.7;
    }
}


/* Dark mode styles */
:deep(.dark) .hours-label {
    background-color: #1F2937;
    color: white;
    border-color: #374151;
}

:deep(.dark) .hours-label.text-red-400 {
    background-color: rgba(248, 113, 113, 0.1);
    border-color: rgba(248, 113, 113, 0.2);
}

:deep(.dark) .week-display {
    background-color: #4338CA;
}

:deep(.dark) .bracket-line,
:deep(.dark) .bracket-left,
:deep(.dark) .bracket-right {
    background-color: #4338CA;
}
</style>
