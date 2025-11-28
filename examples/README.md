# Digipost SDK Examples

This directory contains example code demonstrating how to use the Digipost PHP SDK.

## Prerequisites

Before running these examples, you need:

1. A Digipost enterprise account with API access
2. A PKCS#12 certificate (.p12 file) for authentication
3. Your sender ID from Digipost

## Examples

| File | Description |
|------|-------------|
| `01-basic-setup.php` | How to set up the Digipost client with your certificate |
| `02-send-message.php` | Send a simple message to a recipient |
| `03-identify-recipient.php` | Check if a recipient has a Digipost account |
| `04-send-with-attachments.php` | Send a message with multiple document attachments |
| `05-send-with-print-fallback.php` | Send with physical mail fallback for non-Digipost users |
| `06-send-invoice.php` | Send an invoice with payment information |
| `07-send-appointment.php` | Send an appointment confirmation |
| `08-send-with-notifications.php` | Send with SMS and email notifications |
| `09-batch-sending.php` | Send multiple messages in a batch |
| `10-archive-documents.php` | Store documents in the archive |
| `11-inbox-operations.php` | Read and manage inbox documents |
| `12-document-events.php` | Track document delivery and reading events |
| `13-autocomplete.php` | Search for Digipost recipients |
| `14-error-handling.php` | Handle errors and exceptions |

## Running Examples

1. Install dependencies:
   ```bash
   composer install
   ```

2. Update the example file with your credentials:
   - Replace `/path/to/certificate.p12` with your certificate path
   - Replace `password` with your certificate password
   - Replace `123456` with your sender ID

3. Run the example:
   ```bash
   php examples/02-send-message.php
   ```

## Test Environment

For testing, use the test environment configuration:

```php
$config = DigipostClientConfig::test();
```

This will send requests to Digipost's test API instead of production.

## Getting Help

- [Digipost API Documentation](https://digipost.github.io/digipost-technical-docs/)
- [SDK Repository](https://github.com/coretrekas/digipost)

