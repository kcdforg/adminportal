# KCDF monorepo — GitHub Actions deployment

## Files

Copy `.github/workflows/deploy.yml` into the root of the GitHub monorepo.

## 1. Verify prerequisites on HestiaCP VPS

Log in as `kcdf` (or use an administrator to inspect permissions):

```bash
command -v rsync
command -v ssh
ls -ld /home/kcdf/web/{api,admin,parents}.kcdfindia.com/public_html
```

Confirm that the SSH login user can write to **each** destination. Do not use `chmod 777` or run deployments as root. The workflow uses the `kcdf` Linux user by default through GitHub secret `SSH_USER`.

## 2. Generate a dedicated SSH key on your own computer

```bash
ssh-keygen -t ed25519 -C 'kcdf-github-actions-deploy' -f ./kcdf_actions_deploy -N ''
```

Add the *public* key to the deployment user's `~/.ssh/authorized_keys` on the VPS, e.g. for `kcdf`:

```bash
sudo -u kcdf mkdir -p /home/kcdf/.ssh
sudo -u kcdf chmod 700 /home/kcdf/.ssh
sudo -u kcdf sh -c 'cat >> /home/kcdf/.ssh/authorized_keys' < ./kcdf_actions_deploy.pub
sudo chmod 600 /home/kcdf/.ssh/authorized_keys
sudo chown kcdf:kcdf /home/kcdf/.ssh/authorized_keys
```

The command above assumes the key file is present on the machine executing it. If generating the key locally, transfer only the public key or use `ssh-copy-id -i ./kcdf_actions_deploy.pub kcdf@YOUR_SERVER_IP` instead. Do not copy the private key onto the VPS.

## 3. Pin the VPS SSH host key

From a trusted computer, obtain the host public key and independently verify its fingerprint against the VPS console:

```bash
ssh-keyscan -H YOUR_SERVER_IP
ssh-keygen -lf /etc/ssh/ssh_host_ed25519_key.pub  # run this on the VPS
```

Store the verified `ssh-keyscan` output in the GitHub secret `SSH_KNOWN_HOSTS`. Do not skip verification or disable strict host-key checking.

## 4. Configure GitHub repository secrets

Repository > Settings > Secrets and variables > Actions > New repository secret:

- `SSH_HOST`: VPS IP or SSH hostname
- `SSH_USER`: `kcdf` (or the verified deployment user)
- `SSH_PRIVATE_KEY`: entire content of `kcdf_actions_deploy` private key
- `SSH_KNOWN_HOSTS`: verified `known_hosts` entry for `SSH_HOST`

The SSH hostname must match the host key entry exactly. If you use a nonstandard SSH port, adjust the workflow's SSH and rsync options.

Create a GitHub Environment named `production` if environment protections are desired. The jobs refer to it.

## 5. Confirm project build commands and outputs

- `kcdf-admin-app`: `npm ci && npm run build`, producing exactly one `dist/**/index.html` (often `dist/<project>/browser/index.html`).
- `kcdf-parents-app`: `npm ci && npm run build`, producing `www/index.html`.
- Both frontend folders must contain committed `package-lock.json` files.
- The API job performs PHP syntax checks. If Composer packages are required, ensure they are included in the deployed source or add a suitable `composer install --no-dev --optimize-autoloader` build step and deploy the resulting `vendor/` directory.

Review frontend environment configuration so production builds point at the correct API endpoint. Frontend environment values are public once bundled; never embed private secrets in frontend source.

## 6. Understand file preservation

API rsync intentionally has **no `--delete`**. It excludes `.env`, `.env-*`, `storage/`, `.git/`, `.github/`, and `node_modules/`. This protects persistent files but leaves obsolete API source files in place. Plan a separate controlled cleanup if necessary.

Angular and Ionic rsync use `--delete` to remove obsolete build assets, excluding `.htaccess` and `.well-known/`. **Review the destination folders before the first deployment:** any other server-only files in these frontend webroots may be deleted. Move unrelated persistent files elsewhere or add explicit exclusions.

Back up production before the first run. This workflow does not implement automatic backups or rollback.

## 7. First deployment

1. Commit the workflow to `main`. A change to `.github/workflows/deploy.yml` triggers all three deployments.
2. Watch GitHub > Actions > Deploy KCDF.
3. Confirm each application's URL and refresh deep links to check server-side SPA rewrites.
4. For later pushes, only changed application directories deploy. `workflow_dispatch` manually deploys all three.

## 8. Important constraints

- The workflow is triggered on every push to `main`, but a deployment job runs only when its application directory or the workflow file changed.
- GitHub-hosted runners build Angular/Ionic, not the VPS.
- Concurrent workflow runs are serialized (`cancel-in-progress: false`), though a newer push may supersede a pending run depending on GitHub's concurrency behavior. For strict per-commit deployment, use a dedicated deployment queue.
- The workflow deploys directly into live directories; there is no atomic release switch or rollback. For zero-downtime deployment, use release directories and a symlink switch instead.
- Keep the SSH private key and server host key in GitHub Actions secrets; protect the `main` branch and restrict who can change workflow files.
