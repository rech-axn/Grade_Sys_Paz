import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Grade Calculator Alpine Component
Alpine.data('gradeCalculator', (initial = {}) => ({
    prelim: initial.prelim ?? '',
    midterm: initial.midterm ?? '',
    finals: initial.finals ?? '',
    notes: initial.notes ?? '',
    studentId: initial.student_id ?? '',
    subjectId: initial.subject_id ?? '',
    
    get computedGrade() {
        const p = parseFloat(this.prelim);
        const m = parseFloat(this.midterm);
        const f = parseFloat(this.finals);
        if (!isNaN(p) && !isNaN(m) && !isNaN(f)) {
            return ((p * 0.3) + (m * 0.3) + (f * 0.4)).toFixed(2);
        }
        return null;
    },

    get status() {
        if (this.computedGrade !== null) {
            return parseFloat(this.computedGrade) >= 75.0 ? 'Passed' : 'Failed';
        }
        return 'Pending';
    },

    setValues(row) {
        this.studentId = row.student_id ?? '';
        this.subjectId = row.subject_id ?? '';
        this.prelim = row.prelim ?? '';
        this.midterm = row.midterm ?? '';
        this.finals = row.finals ?? '';
        this.notes = row.notes ?? '';
    }
}));

// Table search filter Alpine Component
Alpine.data('tableFilter', () => ({
    search: '',
    matchRow(text) {
        if (!this.search) return true;
        return text.toLowerCase().includes(this.search.toLowerCase());
    }
}));

Alpine.start();
