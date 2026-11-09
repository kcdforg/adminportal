/* Display the current worker status without reloading the dashboard. */
async function refreshDeploymentStatus() {
    try {
        const response = await fetch('/status.php', { cache: 'no-store' });

        if (!response.ok) {
            return;
        }

        const status = await response.json();
        const state = document.getElementById('deployment-state');
        const details = document.getElementById('deployment-detail');
        const deployButton = document.getElementById('deploy-button');

        state.textContent = `${status.status} — ${status.step || ''}`;
        details.textContent = [status.commit, status.updated_at, status.error]
            .filter(Boolean)
            .join(' · ');

        deployButton.disabled = ['queued', 'running'].includes(status.status);
    } catch (error) {
        // Temporary network errors should not disrupt the page.
        console.warn('Unable to refresh deployment status.', error);
    }
}

refreshDeploymentStatus();
setInterval(refreshDeploymentStatus, 3000);
