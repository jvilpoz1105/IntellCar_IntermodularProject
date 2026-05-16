import json
import os
import urllib.request
import urllib.error
import urllib.parse
import boto3
import logging

logging.basicConfig(level=logging.INFO)
logger = logging.getLogger()


def lambda_handler(event, context):
    """
    Lambda triggered by S3 ObjectCreated event.
    Notifies Laravel backend about the new image upload.
    """
    logger.info("Lambda triggered by S3 event")

    try:
        bucket_name = event['Records'][0]['s3']['bucket']['name']
        object_key = urllib.parse.unquote_plus(event['Records'][0]['s3']['object']['key'])

        logger.info(f"New file uploaded: {object_key} in bucket {bucket_name}")

        internal_api_url = os.environ.get('INTERNAL_API_URL')
        internal_api_token = os.environ.get('INTERNAL_API_TOKEN')

        if not internal_api_url or not internal_api_token:
            logger.error("Missing environment variables")
            return {
                'statusCode': 500,
                'body': json.dumps('Missing environment configuration')
            }

        payload = json.dumps({
            'key': object_key,
            'bucket': bucket_name
        })

        headers = {
            'Content-Type': 'application/json',
            'X-Internal-Token': internal_api_token
        }

        req = urllib.request.Request(
            internal_api_url,
            data=payload.encode('utf-8'),
            headers=headers,
            method='PATCH'
        )

        with urllib.request.urlopen(req, timeout=10) as response:
            response_body = response.read().decode('utf-8')
            logger.info(f"Laravel API responded: {response.status}")

            return {
                'statusCode': 200,
                'body': json.dumps({
                    'status': 'success',
                    'key': object_key,
                    'response': json.loads(response_body) if response_body else {}
                })
            }

    except urllib.error.URLError as e:
        logger.error(f"Error calling Laravel API: {e.reason}")
        return {
            'statusCode': 502,
            'body': json.dumps({'error': 'Failed to notify backend', 'details': str(e)})
        }

    except Exception as e:
        logger.error(f"Unexpected error: {str(e)}")
        return {
            'statusCode': 500,
            'body': json.dumps({'error': 'Internal error', 'details': str(e)})
        }