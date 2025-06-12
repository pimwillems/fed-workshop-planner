import { prisma, transformWorkshop, transformSubjectToDb } from '~/utils/db'

export default defineEventHandler(async (event) => {
  try {
    const query = getQuery(event)
    
    // Build where clause for filtering
    const where: any = {}
    
    if (query.subject) {
      where.subject = transformSubjectToDb(query.subject as string)
    }
    
    if (query.date) {
      where.date = query.date as string
    }
    
    if (query.teacherId) {
      where.teacherId = query.teacherId as string
    }

    // Fetch workshops from database
    const workshops = await prisma.workshop.findMany({
      where,
      include: {
        teacher: {
          select: {
            id: true,
            email: true,
            name: true,
            role: true,
            createdAt: true,
            updatedAt: true
          }
        }
      },
      orderBy: {
        date: 'asc'
      }
    })

    // Transform workshops for frontend
    const transformedWorkshops = workshops.map(transformWorkshop)

    return {
      success: true,
      data: {
        workshops: transformedWorkshops
      }
    }
  } catch (error: any) {
    console.error('Error fetching workshops:', error)
    
    throw createError({
      statusCode: 500,
      statusMessage: 'Failed to fetch workshops'
    })
  }
})