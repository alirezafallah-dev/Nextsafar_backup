import { NextResponse } from 'next/server';

export async function GET() {
  return NextResponse.json({
    counts: {
      hotel: 1247,
      destination: 89,
      tour: 234,
      visa: 156,
      travelguide: 342,
      restaurant: 458,
    },
  });
}