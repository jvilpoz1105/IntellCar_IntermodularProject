import { from, Observable } from 'rxjs';
import { switchMap } from 'rxjs/operators';

export interface PresignedUrlResponse {
  upload_url: string;
  key: string;
  expires: string;
}

export class S3UploadService {
  /**
   * Uploads a file to S3 using a presigned URL obtained from the backend.
   * @param file The file to upload.
   * @param getPresignedUrl A function that returns an Observable with the presigned URL details.
   */
  uploadFile(
    file: File,
    getPresignedUrl: (filename: string, contentType: string) => Observable<PresignedUrlResponse>
  ): Observable<{ key: string; status: string }> {
    return getPresignedUrl(file.name, file.type).pipe(
      switchMap((response: PresignedUrlResponse) => {
        const uploadUrl = response.upload_url;
        const key = response.key;

        // Perform the PUT request directly to S3
        return from(
          fetch(uploadUrl, {
            method: 'PUT',
            body: file,
            headers: {
              'Content-Type': file.type,
            },
          }).then((res) => {
            if (!res.ok) {
              throw new Error(`S3 upload failed: ${res.statusText}`);
            }
            return { key, status: 'uploaded' };
          })
        );
      })
    );
  }
}
