import { EPOS_ENDPOINTS } from './api-endpoints';
import { eposApiGet } from './api-client';

export async function getDashboardData() {
  const [projects, leads, opportunities, tasks, contracts, signatures] = await Promise.all([
    eposApiGet(EPOS_ENDPOINTS.projects),
    eposApiGet(EPOS_ENDPOINTS.leads),
    eposApiGet(EPOS_ENDPOINTS.opportunities),
    eposApiGet(EPOS_ENDPOINTS.projectTasks),
    eposApiGet(EPOS_ENDPOINTS.contracts),
    eposApiGet(EPOS_ENDPOINTS.documentSignatures)
  ]);

  return {
    projects,
    leads,
    opportunities,
    tasks,
    contracts,
    signatures
  };
}
