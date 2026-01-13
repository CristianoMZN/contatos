# Google Cloud Run and Firestore Deployment Guide

This guide explains how to deploy the Contatos application to Google Cloud Run with Firebase Firestore integration.

## Prerequisites

1. **Google Cloud Platform Account**
   - Active GCP project
   - Billing enabled
   - Cloud Run API enabled
   - Container Registry API enabled
   - Firestore API enabled

2. **GitHub Repository Secrets**
   The following secrets must be configured in your GitHub repository:
   
   - `GCP_SA_KEY`: Service account key JSON
   - `GCP_PROJECT_ID`: Your GCP project ID
   - `FIREBASE_CREDENTIALS`: Firebase service account credentials JSON

## Setup Instructions

### 1. Create Google Cloud Service Account

```bash
# Set your project ID
export PROJECT_ID="your-project-id"

# Create service account
gcloud iam service-accounts create contatos-deployer \
    --display-name="Contatos Deployer" \
    --project=${PROJECT_ID}

# Grant necessary permissions
gcloud projects add-iam-policy-binding ${PROJECT_ID} \
    --member="serviceAccount:contatos-deployer@${PROJECT_ID}.iam.gserviceaccount.com" \
    --role="roles/run.admin"

gcloud projects add-iam-policy-binding ${PROJECT_ID} \
    --member="serviceAccount:contatos-deployer@${PROJECT_ID}.iam.gserviceaccount.com" \
    --role="roles/storage.admin"

gcloud projects add-iam-policy-binding ${PROJECT_ID} \
    --member="serviceAccount:contatos-deployer@${PROJECT_ID}.iam.gserviceaccount.com" \
    --role="roles/iam.serviceAccountUser"

# Create and download key
gcloud iam service-accounts keys create key.json \
    --iam-account=contatos-deployer@${PROJECT_ID}.iam.gserviceaccount.com
```

### 2. Create Firebase Service Account

```bash
# Create Firestore service account
gcloud iam service-accounts create contatos-firestore \
    --display-name="Contatos Firestore" \
    --project=${PROJECT_ID}

# Grant Firestore permissions
gcloud projects add-iam-policy-binding ${PROJECT_ID} \
    --member="serviceAccount:contatos-firestore@${PROJECT_ID}.iam.gserviceaccount.com" \
    --role="roles/datastore.user"

# Create and download key
gcloud iam service-accounts keys create firestore-key.json \
    --iam-account=contatos-firestore@${PROJECT_ID}.iam.gserviceaccount.com
```

### 3. Configure GitHub Secrets

1. Go to your GitHub repository
2. Navigate to **Settings** → **Secrets and variables** → **Actions**
3. Add the following secrets:

   - **GCP_SA_KEY**: Paste the entire contents of `key.json`
   - **GCP_PROJECT_ID**: Your GCP project ID (e.g., `contatos-app-123456`)
   - **FIREBASE_CREDENTIALS**: Paste the entire contents of `firestore-key.json` as a single-line JSON string

   To convert multi-line JSON to single-line:
   ```bash
   cat firestore-key.json | jq -c
   ```

### 4. Enable Required APIs

```bash
# Enable Cloud Run API
gcloud services enable run.googleapis.com --project=${PROJECT_ID}

# Enable Container Registry API
gcloud services enable containerregistry.googleapis.com --project=${PROJECT_ID}

# Enable Firestore API
gcloud services enable firestore.googleapis.com --project=${PROJECT_ID}
```

### 5. Initialize Firestore

1. Go to [Firebase Console](https://console.firebase.google.com/)
2. Select your project or create a new one
3. Go to **Firestore Database**
4. Click **Create Database**
5. Choose **Production mode**
6. Select a region (preferably `us-central1` to match Cloud Run)

## Deployment

### Automatic Deployment

The application automatically deploys to Google Cloud Run when you push to the `master` branch.

```bash
git push origin master
```

### Manual Deployment

You can also deploy manually using the Google Cloud SDK:

```bash
# Build the Docker image
docker build -f Dockerfile.cloudrun -t gcr.io/${PROJECT_ID}/contatos-app:latest .

# Push to Container Registry
docker push gcr.io/${PROJECT_ID}/contatos-app:latest

# Deploy to Cloud Run
gcloud run deploy contatos-app \
    --image=gcr.io/${PROJECT_ID}/contatos-app:latest \
    --region=us-central1 \
    --platform=managed \
    --allow-unauthenticated \
    --set-env-vars="GCP_PROJECT_ID=${PROJECT_ID},FIREBASE_CREDENTIALS=$(cat firestore-key.json | jq -c)"
```

## Architecture

### Docker Image (Dockerfile.cloudrun)

- **Base**: php:8.1-apache
- **Extensions**: grpc, protobuf, pdo_mysql
- **Web Server**: Apache with mod_rewrite
- **Document Root**: /var/www/html/public
- **Port**: Dynamic (set by Cloud Run via PORT environment variable)

### Firestore Connection

The application uses `App\Config\FirestoreConnection` class to manage Firestore connections:

```php
use App\Config\FirestoreConnection;

// Get Firestore client instance
$db = FirestoreConnection::getDb();

// Example: Add a document
$db->collection('contacts')->add([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'created_at' => new \DateTime()
]);

// Example: Query documents
$contacts = $db->collection('contacts')
    ->where('user_id', '=', $userId)
    ->documents();
```

### Environment Variables

The following environment variables are required at runtime:

- **GCP_PROJECT_ID**: Your GCP project ID
- **FIREBASE_CREDENTIALS**: Firebase service account credentials (JSON string)
- **PORT**: Automatically set by Cloud Run (default: 8080)

## Monitoring and Logs

### View Logs

```bash
# View Cloud Run logs
gcloud run services logs read contatos-app --region=us-central1 --limit=100

# Stream logs in real-time
gcloud run services logs tail contatos-app --region=us-central1
```

### View Service Details

```bash
# Get service information
gcloud run services describe contatos-app --region=us-central1

# Get service URL
gcloud run services describe contatos-app \
    --region=us-central1 \
    --format='value(status.url)'
```

## Troubleshooting

### Common Issues

1. **Build fails with "grpc extension not found"**
   - Ensure the Dockerfile.cloudrun is being used
   - Check that PECL extensions are installed correctly

2. **Firestore connection fails**
   - Verify FIREBASE_CREDENTIALS is set correctly
   - Check that GCP_PROJECT_ID matches your project
   - Ensure Firestore API is enabled

3. **Apache not listening on PORT**
   - The Dockerfile configures Apache to read PORT from environment
   - Cloud Run automatically sets this variable

4. **Permission denied errors**
   - Verify service account has necessary IAM roles
   - Check Cloud Run service settings

### Debug Mode

To enable debug mode, add to your Cloud Run service:

```bash
gcloud run services update contatos-app \
    --region=us-central1 \
    --set-env-vars="DEBUG=true"
```

## Cost Optimization

- Cloud Run charges only for actual usage (CPU and memory during request processing)
- Firestore charges based on document reads/writes and storage
- Container Registry storage is billed monthly

**Estimated costs for low traffic:**
- Cloud Run: ~$0-5/month
- Firestore: ~$0-1/month
- Container Registry: ~$0.10/month

## Security Best Practices

1. **Never commit secrets to repository**
   - Use GitHub Secrets for sensitive data
   - Use `.env` files only for local development

2. **Limit service account permissions**
   - Follow principle of least privilege
   - Create separate service accounts for different purposes

3. **Enable authentication when needed**
   - Use Cloud IAM for service-to-service calls
   - Consider Firebase Authentication for user access

4. **Regular security updates**
   - Keep Docker base image updated
   - Update PHP and composer dependencies regularly

## References

- [Google Cloud Run Documentation](https://cloud.google.com/run/docs)
- [Firebase Firestore Documentation](https://firebase.google.com/docs/firestore)
- [Google Cloud Firestore PHP Client](https://github.com/googleapis/google-cloud-php-firestore)
- [GitHub Actions Documentation](https://docs.github.com/en/actions)

## Support

For issues or questions:
- Open an issue on GitHub
- Check Cloud Run logs for error details
- Review Firestore logs in Firebase Console
