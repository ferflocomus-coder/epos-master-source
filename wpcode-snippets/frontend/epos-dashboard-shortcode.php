<?php
/**
 * EPOS Dashboard Shortcode
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('epos_dashboard_shortcode_render')) {
    function epos_dashboard_shortcode_render() {
        ob_start();
        ?>
        <div id="epos-dashboard-root">
            <div class="epos-dashboard-shell">
                <h1>EPOS Dashboard</h1>
                <p>Dashboard mount point initialized.</p>
                <div id="epos-kpi-projects">Active Projects: loading...</div>
                <div id="epos-kpi-leads">New Leads: loading...</div>
                <div id="epos-kpi-signatures">Pending Signatures: loading...</div>
                <div id="epos-pipeline-list">Pipeline: loading...</div>
                <div id="epos-task-list">Tasks: loading...</div>
            </div>
        </div>
        <script>
        (function () {
            async function getJson(url) {
                const response = await fetch(url, {
                    method: 'GET',
                    credentials: 'include',
                    headers: { 'Content-Type': 'application/json' }
                });

                if (!response.ok) {
                    throw new Error('Request failed: ' + response.status);
                }

                return response.json();
            }

            async function loadDashboard() {
                try {
                    const [projects, leads, signatures, opportunities, tasks] = await Promise.all([
                        getJson('/wp-json/ep/v1/projects'),
                        getJson('/wp-json/ep/v1/leads'),
                        getJson('/wp-json/ep/v1/document-signatures'),
                        getJson('/wp-json/ep/v1/opportunities'),
                        getJson('/wp-json/ep/v1/project-tasks')
                    ]);

                    const projectsCount = projects?.data?.pagination?.total ?? 0;
                    const leadsCount = leads?.data?.pagination?.total ?? 0;
                    const signaturesCount = signatures?.data?.pagination?.total ?? 0;
                    const opportunitiesCount = opportunities?.data?.pagination?.total ?? 0;
                    const tasksCount = tasks?.data?.pagination?.total ?? 0;

                    document.getElementById('epos-kpi-projects').textContent = 'Active Projects: ' + projectsCount;
                    document.getElementById('epos-kpi-leads').textContent = 'New Leads: ' + leadsCount;
                    document.getElementById('epos-kpi-signatures').textContent = 'Pending Signatures: ' + signaturesCount;
                    document.getElementById('epos-pipeline-list').textContent = 'Pipeline records: ' + opportunitiesCount;
                    document.getElementById('epos-task-list').textContent = 'Tasks: ' + tasksCount;
                } catch (error) {
                    document.getElementById('epos-dashboard-root').setAttribute('data-epos-error', error.message || 'Unknown error');
                }
            }

            loadDashboard();
        })();
        </script>
        <?php
        return ob_get_clean();
    }
}

if (!function_exists('epos_dashboard_shortcode_register')) {
    function epos_dashboard_shortcode_register() {
        add_shortcode('epos_dashboard', 'epos_dashboard_shortcode_render');
    }
}
add_action('init', 'epos_dashboard_shortcode_register');
