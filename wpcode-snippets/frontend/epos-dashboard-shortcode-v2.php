<?php
/**
 * EPOS Dashboard Shortcode V2
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('epos_dashboard_shortcode_v2_render')) {
    function epos_dashboard_shortcode_v2_render() {
        ob_start();
        ?>
        <div id="epos-dashboard-root-v2" style="min-height:100vh;background:#0a0a0a;color:#fff;font-family:Arial,sans-serif;display:flex;">
            <aside style="width:260px;background:#000;border-right:1px solid #27272a;padding:24px 16px;box-sizing:border-box;">
                <div style="font-size:22px;font-weight:700;color:#BF953F;margin-bottom:30px;">EPOS</div>
                <div style="display:flex;flex-direction:column;gap:10px;">
                    <div style="padding:12px 14px;background:#18181b;border:1px solid #27272a;border-radius:12px;">Dashboard</div>
                    <div style="padding:12px 14px;color:#a1a1aa;">CRM</div>
                    <div style="padding:12px 14px;color:#a1a1aa;">Projects</div>
                    <div style="padding:12px 14px;color:#a1a1aa;">Estimators</div>
                    <div style="padding:12px 14px;color:#a1a1aa;">Contracts</div>
                </div>
            </aside>
            <div style="flex:1;display:flex;flex-direction:column;">
                <header style="height:80px;background:#111;border-bottom:1px solid #27272a;display:flex;align-items:center;justify-content:space-between;padding:0 24px;box-sizing:border-box;">
                    <div style="font-size:28px;font-weight:700;">Dashboard</div>
                    <div style="color:#a1a1aa;">Evolution Power OS</div>
                </header>
                <main style="padding:24px;box-sizing:border-box;">
                    <div style="display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:16px;margin-bottom:24px;">
                        <div style="background:#18181b;border:1px solid #27272a;border-radius:18px;padding:20px;">
                            <div style="font-size:12px;color:#a1a1aa;margin-bottom:10px;">ACTIVE PROJECTS</div>
                            <div id="epos-v2-projects" style="font-size:34px;font-weight:700;color:#BF953F;">...</div>
                        </div>
                        <div style="background:#18181b;border:1px solid #27272a;border-radius:18px;padding:20px;">
                            <div style="font-size:12px;color:#a1a1aa;margin-bottom:10px;">NEW LEADS</div>
                            <div id="epos-v2-leads" style="font-size:34px;font-weight:700;">...</div>
                        </div>
                        <div style="background:#18181b;border:1px solid #27272a;border-radius:18px;padding:20px;">
                            <div style="font-size:12px;color:#a1a1aa;margin-bottom:10px;">PENDING SIGNATURES</div>
                            <div id="epos-v2-signatures" style="font-size:34px;font-weight:700;">...</div>
                        </div>
                        <div style="background:#18181b;border:1px solid #27272a;border-radius:18px;padding:20px;">
                            <div style="font-size:12px;color:#a1a1aa;margin-bottom:10px;">PIPELINE RECORDS</div>
                            <div id="epos-v2-opportunities" style="font-size:34px;font-weight:700;">...</div>
                        </div>
                    </div>
                    <div style="display:grid;grid-template-columns:2fr 1fr;gap:16px;">
                        <div style="background:#18181b;border:1px solid #27272a;border-radius:18px;padding:20px;">
                            <div style="font-size:18px;font-weight:700;margin-bottom:16px;">Pipeline Snapshot</div>
                            <div id="epos-v2-pipeline-list" style="color:#a1a1aa;">Loading pipeline...</div>
                        </div>
                        <div style="background:#18181b;border:1px solid #27272a;border-radius:18px;padding:20px;">
                            <div style="font-size:18px;font-weight:700;margin-bottom:16px;">My Tasks</div>
                            <div id="epos-v2-task-list" style="color:#a1a1aa;">Loading tasks...</div>
                        </div>
                    </div>
                </main>
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
                if (!response.ok) throw new Error('Request failed: ' + response.status);
                return response.json();
            }

            function getItems(result) {
                return result && result.data && result.data.items ? result.data.items : [];
            }

            function getTotal(result) {
                return result && result.data && result.data.pagination ? result.data.pagination.total : 0;
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

                    document.getElementById('epos-v2-projects').textContent = getTotal(projects);
                    document.getElementById('epos-v2-leads').textContent = getTotal(leads);
                    document.getElementById('epos-v2-signatures').textContent = getTotal(signatures);
                    document.getElementById('epos-v2-opportunities').textContent = getTotal(opportunities);

                    const opportunityItems = getItems(opportunities).slice(0, 5);
                    const taskItems = getItems(tasks).slice(0, 5);

                    document.getElementById('epos-v2-pipeline-list').innerHTML = opportunityItems.length
                        ? opportunityItems.map(function (item) {
                            return '<div style="padding:10px 0;border-bottom:1px solid #27272a;"><strong>' + (item.title || 'Untitled') + '</strong><br><span style="color:#a1a1aa;font-size:12px;">' + (item.status || 'n/a') + '</span></div>';
                        }).join('')
                        : 'No records found.';

                    document.getElementById('epos-v2-task-list').innerHTML = taskItems.length
                        ? taskItems.map(function (item) {
                            return '<div style="padding:10px 0;border-bottom:1px solid #27272a;"><strong>' + (item.title || 'Untitled') + '</strong><br><span style="color:#a1a1aa;font-size:12px;">' + (item.status || 'n/a') + '</span></div>';
                        }).join('')
                        : 'No tasks found.';
                } catch (error) {
                    document.getElementById('epos-v2-pipeline-list').textContent = 'Error loading dashboard.';
                    document.getElementById('epos-v2-task-list').textContent = error.message || 'Unknown error';
                }
            }

            loadDashboard();
        })();
        </script>
        <?php
        return ob_get_clean();
    }
}

if (!function_exists('epos_dashboard_shortcode_v2_register')) {
    function epos_dashboard_shortcode_v2_register() {
        add_shortcode('epos_dashboard_v2', 'epos_dashboard_shortcode_v2_render');
    }
}
add_action('init', 'epos_dashboard_shortcode_v2_register');
