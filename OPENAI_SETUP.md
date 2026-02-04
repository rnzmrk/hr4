# OpenAI Integration Setup Guide

This guide will help you set up OpenAI integration for accurate Philippine income tax calculations in your payroll system.

## Prerequisites

1. OpenAI API Key (from https://platform.openai.com/api-keys)
2. Laravel environment configured

## Setup Steps

### 1. Get Your OpenAI API Key

1. Go to [OpenAI Platform](https://platform.openai.com/)
2. Sign up or log in to your account
3. Navigate to [API Keys](https://platform.openai.com/api-keys)
4. Click "Create new secret key"
5. Give your key a descriptive name (e.g., "Payroll Tax Calculator")
6. Copy the generated key

### 2. Configure Environment Variables

Add the following to your `.env` file:

```env
# OpenAI Configuration
OPENAI_API_KEY=sk-your-actual-api-key-here
OPENAI_ORGANIZATION_ID=org-your-organization-id (optional)
OPENAI_DEFAULT_MODEL=gpt-3.5-turbo
OPENAI_ENABLED=true
OPENAI_FALLBACK_TO_LOCAL=true
```

**Important:**
- Replace `sk-your-actual-api-key-here` with your actual OpenAI API key
- Keep your API key secure and never commit it to version control
- `OPENAI_ORGANIZATION_ID` is optional - only needed if you belong to multiple organizations

### 3. Configuration Options

| Option | Default | Description |
|--------|---------|-------------|
| `OPENAI_API_KEY` | Required | Your OpenAI API key |
| `OPENAI_ORGANIZATION_ID` | null | Your OpenAI organization ID (optional) |
| `OPENAI_DEFAULT_MODEL` | gpt-3.5-turbo | OpenAI model to use for calculations |
| `OPENAI_ENABLED` | true | Enable/disable OpenAI integration |
| `OPENAI_FALLBACK_TO_LOCAL` | true | Fall back to local calculation if OpenAI fails |

### 4. How It Works

1. **Primary Method**: The system first tries to calculate tax using OpenAI with the latest BIR tax rates
2. **Fallback Method**: If OpenAI is unavailable or returns invalid results, it falls back to local calculation
3. **Validation**: All OpenAI results are validated to ensure they're reasonable (0 ≤ tax ≤ taxable income)

### 5. Cost Considerations

- **gpt-3.5-turbo** costs approximately $0.002 per 1K tokens
- Each tax calculation uses ~200 tokens
- Estimated cost: ~$0.0004 per calculation
- For 1,000 calculations: ~$0.40

### 6. Testing the Integration

1. Add your API key to `.env`
2. Test with a sample payroll calculation
3. Check Laravel logs for any OpenAI-related warnings
4. Verify calculations match expected BIR tax rates

### 7. Troubleshooting

#### Common Issues

**Issue**: "OpenAI tax calculation failed, using local calculation"
- **Solution**: Check your API key and internet connection

**Issue**: Invalid tax amounts from OpenAI
- **Solution**: System automatically falls back to local calculation

**Issue**: API rate limits
- **Solution**: Enable fallback to local calculation in config

#### Disabling OpenAI

To disable OpenAI integration and use only local calculations:

```env
OPENAI_ENABLED=false
```

Or temporarily disable without changing code:

```env
OPENAI_API_KEY=your_openai_api_key_here
```

### 8. Security Notes

- Never commit your API key to version control
- Use environment variables for all sensitive configuration
- Regularly rotate your API keys
- Monitor your OpenAI usage and costs

### 9. Benefits of OpenAI Integration

- **Real-time Updates**: Always uses the latest tax rate information
- **Accuracy**: Reduces human error in tax calculations
- **Flexibility**: Easy to update tax rules without code changes
- **Reliability**: Automatic fallback to local calculation ensures system always works

## Support

If you encounter issues with the OpenAI integration:

1. Check your `.env` configuration
2. Verify your API key is valid and active
3. Check Laravel logs at `storage/logs/laravel.log`
4. Ensure you have sufficient OpenAI API credits

For questions about Philippine tax calculations, consult the latest BIR guidelines or a tax professional.
