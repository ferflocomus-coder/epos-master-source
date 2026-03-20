import { useDashboardData } from '../hooks/useDashboardData';

export function initDashboardPage() {
  return {
    name: 'dashboard',
    hook: useDashboardData
  };
}
