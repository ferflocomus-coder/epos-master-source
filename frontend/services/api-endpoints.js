export const EPOS_API_BASE = '/wp-json/ep/v1';

export const EPOS_ENDPOINTS = {
  bootstrapStatus: `${EPOS_API_BASE}/system/bootstrap-status`,
  businesses: `${EPOS_API_BASE}/businesses`,
  clients: `${EPOS_API_BASE}/clients`,
  leads: `${EPOS_API_BASE}/leads`,
  opportunities: `${EPOS_API_BASE}/opportunities`,
  estimators: `${EPOS_API_BASE}/estimators`,
  estimatorItems: `${EPOS_API_BASE}/estimator-items`,
  proposals: `${EPOS_API_BASE}/proposals`,
  contracts: `${EPOS_API_BASE}/contracts`,
  projects: `${EPOS_API_BASE}/projects`,
  projectStages: `${EPOS_API_BASE}/project-stages`,
  projectTasks: `${EPOS_API_BASE}/project-tasks`,
  installers: `${EPOS_API_BASE}/installers`,
  commissions: `${EPOS_API_BASE}/commissions`,
  documents: `${EPOS_API_BASE}/documents`,
  documentSignatures: `${EPOS_API_BASE}/document-signatures`,
  notifications: `${EPOS_API_BASE}/notifications`,
  roles: `${EPOS_API_BASE}/roles`,
  systemUsers: `${EPOS_API_BASE}/system-users`
};
