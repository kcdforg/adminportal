# API Integration

This document records the integration expectations and outstanding checks between the administration portal and its API.

## Integration expectations

- The portal uses one configured API base URL.
- Protected requests include the signed-in user’s access credential.
- Request fields and response data follow the API contract for each feature.
- Errors are presented clearly and do not appear as successful actions.
- Portal visibility rules complement, but do not replace, server-side authorization.

## Integration items to resolve

- Confirm program and trainer updates complete successfully from their forms.
- Confirm batch updates and batch-member actions use supported API operations.
- Confirm session updates and attendance locking complete successfully.
- Confirm direct and broadcast notification requests use the agreed fields and recipients.
- Confirm enrollment cancellation completes and updates the enrollment state.
- Confirm attendance, payment, and enrollment reports have supported data sources and return the expected results.
- Align the trainer update request method with the API contract before verifying trainer edits.

These are integration checks, not approved product behavior. Do not mark the related user journeys complete until they are verified in the running portal and API.
