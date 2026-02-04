/**
 * Evaluation Score Thresholds and Severity Mappings
 * Used across the application for consistent score evaluation
 */

export const SCORE_THRESHOLDS = {
    DANGER: 50,
    WARNING: 80,
    EXCELLENT: 90,
}

/**
 * Get severity level based on score
 * @param {number} score - The evaluation score (0-100)
 * @returns {'danger'|'warn'|'success'} PrimeVue severity variant
 */
export function getScoreSeverity(score) {
    if (score < SCORE_THRESHOLDS.DANGER) return 'danger'
    if (score < SCORE_THRESHOLDS.WARNING) return 'warn'
    return 'success'
}

/**
 * Get human-readable label for score
 * @param {number} score - The evaluation score (0-100)
 * @returns {string} Score quality label
 */
export function getScoreLabel(score) {
    if (score < SCORE_THRESHOLDS.DANGER) return 'Poor Setup'
    if (score < SCORE_THRESHOLDS.WARNING) return 'Fair Setup'
    if (score < SCORE_THRESHOLDS.EXCELLENT) return 'Good Setup'
    return 'Excellent Setup'
}

/**
 * Trade status display mappings
 */
export const TRADE_STATUS = {
    PENDING: { value: 'pending', label: 'Pending', severity: 'warn' },
    ACTIVE: { value: 'active', label: 'Open', severity: 'info' },
    WIN: { value: 'win', label: 'Win', severity: 'success' },
    LOSS: { value: 'loss', label: 'Loss', severity: 'danger' },
    BREAKEVEN: { value: 'breakeven', label: 'Breakeven', severity: 'secondary' },
    CANCELLED: { value: 'cancelled', label: 'Cancelled', severity: 'secondary' },
}

/**
 * Position type mappings
 */
export const POSITION_TYPE = {
    LONG: { value: 'Long', label: 'Long', icon: 'pi-arrow-up', color: 'emerald' },
    SHORT: { value: 'Short', label: 'Short', icon: 'pi-arrow-down', color: 'rose' },
}

/**
 * File upload constraints
 */
export const UPLOAD_LIMITS = {
    MAX_SCREENSHOTS: 5,
    MAX_FILE_SIZE_MB: 5,
    MAX_FILE_SIZE_BYTES: 5 * 1024 * 1024, // 5MB
    ALLOWED_TYPES: ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
}
