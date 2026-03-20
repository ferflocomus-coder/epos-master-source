export async function eposApiGet(url) {
  const response = await fetch(url, {
    method: 'GET',
    credentials: 'include',
    headers: { 'Content-Type': 'application/json' }
  });
  if (!response.ok) throw new Error(`GET failed: ${response.status}`);
  return response.json();
}

export async function eposApiPost(url, payload = {}) {
  const response = await fetch(url, {
    method: 'POST',
    credentials: 'include',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(payload)
  });
  if (!response.ok) throw new Error(`POST failed: ${response.status}`);
  return response.json();
}

export async function eposApiPut(url, payload = {}) {
  const response = await fetch(url, {
    method: 'PUT',
    credentials: 'include',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(payload)
  });
  if (!response.ok) throw new Error(`PUT failed: ${response.status}`);
  return response.json();
}
