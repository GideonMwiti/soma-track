/**
 * SomaTrack - API Handler for Interactive UI
 */
const SomaAPI = {
    async call(url, method = 'GET', data = null) {
        const options = {
            method: method,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        };
        
        if (data) {
            if (data instanceof FormData) {
                options.body = data;
            } else {
                options.headers['Content-Type'] = 'application/x-www-form-urlencoded';
                options.body = new URLSearchParams(data).toString();
            }
        }
        
        try {
            const response = await fetch(url, options);
            const result = await response.json();
            return result;
        } catch (error) {
            console.error('API Call failed:', error);
            return { success: false, message: 'Network error or invalid response.' };
        }
    },

    async toggleStepStatus(stepId, status, token) {
        return await this.call(`${SITE_URL}/api/steps.php?action=status&id=${stepId}&status=${status}&token=${token}&format=json`);
    },

    async toggleDraft(stepId, token) {
        return await this.call(`${SITE_URL}/api/steps.php?action=toggle_draft&id=${stepId}&token=${token}&format=json`);
    }
};

// Global helper for toast/alerts (optional, can use bootstrap toasts)
function showToast(message, type = 'success') {
    // Simple alert for now, can be upgraded to toast
    alert(message);
}
