/**
 * offlineOutboxQueue.js — Project OS v11.0 (PRIMITIVE_OFFLINE_OUTBOX_PWA)
 *
 * Bad-Signal & Offline-First Resilience Engine for Indonesian field operations
 * (Cashier POS, Stadium/Event Gate QR Scanners, Courier PoD, Rural NGO Surveys).
 *
 * - Generates lexicographically sortable 26-char Crockford Base32 ULIDs locally on the client
 * - Persists mutations in IndexedDB (`neriahpro_offline_outbox_v11`) when offline or flaky
 * - Automatically flushes queued mutations in FIFO order with `X-Idempotency-Key` upon `online` event
 */

const CROCKFORD_B32 = '0123456789ABCDEFGHJKMNPQRSTVWXYZ';
const DB_NAME = 'neriahpro_offline_outbox_v11';
const STORE_NAME = 'outbox_mutations';

export function generateLocalUlid(timestampMs = Date.now()) {
  let time = Math.floor(timestampMs);
  let timeChars = '';
  for (let i = 9; i >= 0; i -= 1) {
    const mod = time % 32;
    timeChars = CROCKFORD_B32.charAt(mod) + timeChars;
    time = Math.floor(time / 32);
  }

  const randBytes = new Uint8Array(16);
  if (typeof crypto !== 'undefined' && typeof crypto.getRandomValues === 'function') {
    crypto.getRandomValues(randBytes);
  } else {
    for (let i = 0; i < 16; i += 1) {
      randBytes[i] = Math.floor(Math.random() * 256);
    }
  }

  let randChars = '';
  for (let i = 0; i < 16; i += 1) {
    randChars += CROCKFORD_B32.charAt(randBytes[i] % 32);
  }

  return (timeChars + randChars).slice(0, 26);
}

function openOutboxDb() {
  return new Promise((resolve, reject) => {
    if (typeof indexedDB === 'undefined') {
      resolve(null);
      return;
    }
    const req = indexedDB.open(DB_NAME, 1);
    req.onupgradeneeded = () => {
      const db = req.result;
      if (!db.objectStoreNames.contains(STORE_NAME)) {
        db.createObjectStore(STORE_NAME, { keyPath: 'id' });
      }
    };
    req.onsuccess = () => resolve(req.result);
    req.onerror = () => reject(req.error);
  });
}

export async function enqueueOfflineMutation(endpoint, payload, method = 'POST') {
  const id = generateLocalUlid();
  const entry = {
    id,
    idempotency_key: `ulid-outbox-${id}`,
    endpoint,
    method,
    payload,
    queued_at: new Date().toISOString(),
    attempts: 0,
  };

  const db = await openOutboxDb();
  if (!db) {
    return entry;
  }

  await new Promise((resolve, reject) => {
    const tx = db.transaction(STORE_NAME, 'readwrite');
    tx.objectStore(STORE_NAME).put(entry);
    tx.oncomplete = () => resolve(entry);
    tx.onerror = () => reject(tx.error);
  });

  return entry;
}

export async function flushOfflineOutbox() {
  if (typeof navigator !== 'undefined' && navigator.onLine === false) {
    return { flushed: 0, remaining: -1 };
  }

  const db = await openOutboxDb();
  if (!db) {
    return { flushed: 0, remaining: 0 };
  }

  const items = await new Promise((resolve, reject) => {
    const tx = db.transaction(STORE_NAME, 'readonly');
    const req = tx.objectStore(STORE_NAME).getAll();
    req.onsuccess = () => resolve(req.result || []);
    req.onerror = () => reject(req.error);
  });

  items.sort((a, b) => String(a.id).localeCompare(String(b.id)));
  let flushed = 0;

  for (const item of items) {
    try {
      const csrf = typeof document !== 'undefined'
        ? document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        : '';

      const res = await fetch(item.endpoint, {
        method: item.method || 'POST',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
          'X-CSRF-TOKEN': csrf,
          'X-Idempotency-Key': item.idempotency_key,
        },
        body: JSON.stringify({ ...item.payload, client_ulid: item.id }),
      });

      if (res.ok || res.status === 409) {
        await new Promise((resolve) => {
          const delTx = db.transaction(STORE_NAME, 'readwrite');
          delTx.objectStore(STORE_NAME).delete(item.id);
          delTx.oncomplete = () => resolve();
        });
        flushed += 1;
      }
    } catch {
      break;
    }
  }

  return { flushed, remaining: Math.max(0, items.length - flushed) };
}

if (typeof window !== 'undefined') {
  window.addEventListener('online', () => {
    flushOfflineOutbox().catch(() => {});
  });
}

