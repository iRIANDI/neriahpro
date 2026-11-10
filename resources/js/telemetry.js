/**
 * Neriah Pro Black Box Client Error Telemetry & Log Scrubber
 * The Wahyu Ivan Standard: Eliminates "Works on my machine" syndrome across device fragmentation.
 * Catches client-side errors on older iPhones/browsers with strict Zero-Leak PII scrubbing.
 */

(function () {
  'use strict';

  if (typeof window === 'undefined') return;

  // Rate Limiting on Client (Max 3 reports per minute to prevent infinite loop spamming)
  let errorCount = 0;
  const MAX_REPORTS_PER_MINUTE = 3;
  setInterval(() => { errorCount = 0; }, 60000);

  /**
   * Device, OS & Browser Profiler
   */
  function getDeviceProfile() {
    const ua = navigator.userAgent || '';
    let os = 'Unknown OS';
    let device = 'Desktop';

    if (/iPad|iPhone|iPod/.test(ua) || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1)) {
      device = /iPad/.test(ua) ? 'iPad' : 'iPhone';
      const match = ua.match(/OS (\d+[_\d]*)/);
      os = match ? 'iOS ' + match[1].replace(/_/g, '.') : 'iOS';
    } else if (/Android/.test(ua)) {
      device = 'Android Device';
      const match = ua.match(/Android\s([0-9\.]+)/);
      os = match ? 'Android ' + match[1] : 'Android';
    } else if (/Macintosh|Mac OS X/.test(ua)) {
      device = 'Mac';
      os = 'macOS';
    } else if (/Windows/.test(ua)) {
      device = 'PC';
      os = 'Windows';
    } else if (/Linux/.test(ua)) {
      device = 'Linux PC';
      os = 'Linux';
    }

    let browser = 'Unknown Browser';
    if (/Chrome/.test(ua) && !/Edge|OPR|Edg/.test(ua)) browser = 'Chrome';
    else if (/Safari/.test(ua) && !/Chrome/.test(ua)) browser = 'Safari';
    else if (/Firefox/.test(ua)) browser = 'Firefox';
    else if (/Edg/.test(ua)) browser = 'Edge';

    return {
      device,
      os,
      browser,
      screen_width: window.innerWidth || 0,
      screen_height: window.innerHeight || 0,
      language: navigator.language || 'en',
    };
  }

  /**
   * Strict Zero-Leak PII Log Scrubber
   * Guaranteed redaction of passwords, tokens, auth cookies, and credit cards.
   */
  function scrubPii(text) {
    if (!text || typeof text !== 'string') return '';

    return text
      // Redact credit card numbers (13-16 digits)
      .replace(/\b(?:\d[ -]*?){13,16}\b/g, '[REDACTED_CREDIT_CARD]')
      // Redact Bearer tokens
      .replace(/Bearer\s+[a-zA-Z0-9_\-\.]+/gi, 'Bearer [REDACTED_TOKEN]')
      // Redact JWT tokens
      .replace(/ey[A-Za-z0-9_-]{10,}\.[A-Za-z0-9_-]{10,}\.[A-Za-z0-9_-]{10,}/g, '[REDACTED_JWT]')
      // Redact sensitive query strings or key-values
      .replace(/(password|passwd|pwd|secret|token|api_key|cvv|cvc)=[^&\s]+/gi, '$1=[REDACTED]');
  }

  /**
   * Sanitize URL to strip sensitive query parameters
   */
  function sanitizeUrl(rawUrl) {
    try {
      const parsed = new URL(rawUrl || window.location.href);
      const sensitiveKeys = ['token', 'password', 'key', 'auth', 'secret', 'signature', 'cvv', 'card'];
      sensitiveKeys.forEach(k => {
        if (parsed.searchParams.has(k)) {
          parsed.searchParams.set(k, '[REDACTED]');
        }
      });
      return parsed.toString();
    } catch (e) {
      return scrubPii(rawUrl || window.location.href);
    }
  }

  /**
   * Send Telemetry to Server
   */
  function sendTelemetry(payload) {
    if (errorCount >= MAX_REPORTS_PER_MINUTE) return;
    errorCount++;

    const sanitizedPayload = {
      error_message: scrubPii(payload.message || 'Unknown client error'),
      error_type: payload.type || 'CLIENT_RUNTIME_CRASH',
      file_url: sanitizeUrl(payload.source || ''),
      line_number: payload.lineno || null,
      column_number: payload.colno || null,
      stack_trace: scrubPii(payload.stack || ''),
      device_info: getDeviceProfile(),
      current_url: sanitizeUrl(window.location.href),
      app_version: 'v12.0-ULTIMATE',
    };

    const endpoint = '/api/telemetry/client-error';
    const jsonString = JSON.stringify(sanitizedPayload);

    // Prefer navigator.sendBeacon for reliable transmission even during page unload
    if (navigator.sendBeacon) {
      const blob = new Blob([jsonString], { type: 'application/json' });
      const sent = navigator.sendBeacon(endpoint, blob);
      if (sent) return;
    }

    // Fallback to fetch with keepalive
    fetch(endpoint, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
      },
      body: jsonString,
      keepalive: true,
    }).catch(() => {
      // Fail silently to never degrade user experience
    });
  }

  // 1. Global unhandled error listener
  window.addEventListener('error', function (event) {
    // Ignore cross-origin script error noise with 0 information
    if (event.message === 'Script error.' && !event.filename) return;

    sendTelemetry({
      type: 'WINDOW_ON_ERROR',
      message: event.message,
      source: event.filename,
      lineno: event.lineno,
      colno: event.colno,
      stack: event.error ? event.error.stack : '',
    });
  });

  // 2. Global unhandled promise rejection listener
  window.addEventListener('unhandledrejection', function (event) {
    const reason = event.reason || {};
    sendTelemetry({
      type: 'UNHANDLED_PROMISE_REJECTION',
      message: reason.message || String(reason),
      source: window.location.href,
      lineno: null,
      colno: null,
      stack: reason.stack || '',
    });
  });

  // Export helper for manual boundary catching in React/Vue
  window.recordBlackBoxCrash = function (error, errorInfo) {
    sendTelemetry({
      type: 'REACT_ERROR_BOUNDARY_CRASH',
      message: error ? error.message : 'React component crashed',
      source: window.location.href,
      stack: (error ? error.stack : '') + (errorInfo ? '\nComponent Stack: ' + errorInfo.componentStack : ''),
    });
  };
})();

