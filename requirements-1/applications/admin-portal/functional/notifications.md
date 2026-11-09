# Notifications

This document covers administrator-created notifications and the review of received notifications.

## Status

Working draft. Confirm channels, targeting, and delivery expectations.

### ADM-NOT-001 — Review notifications

**Actor:** Signed-in administrators with notification access.

**Requirement:** An administrator can review notifications available to them and manage their read state where supported.

**Acceptance criteria:**
- Read and unread notifications are distinguishable.
- Opening or marking a notification as read updates its displayed state.
- Empty and failed-load states are clear.

### ADM-NOT-002 — Send a direct notification

**Actor:** Administrators with notification-send access.

**Requirement:** An authorized administrator can send a notification to a permitted recipient.

**Acceptance criteria:**
- The recipient and message are validated before sending.
- The sender receives confirmation only when the send action succeeds.
- Failed delivery or request errors are reported clearly.

### ADM-NOT-003 — Broadcast a notification

**Actor:** Administrators with broadcast permission.

**Requirement:** An authorized administrator can send a notification to an approved audience.

**Acceptance criteria:**
- The audience is displayed and confirmed before sending.
- Users outside the selected audience do not receive the notification.
- The sender can distinguish a successful broadcast from a failed or partial send.

## Open questions

- Define supported channels, audience segments, scheduling, and delivery status visibility.
- Confirm whether broadcast sends require confirmation or approval.
