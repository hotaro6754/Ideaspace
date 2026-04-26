import { supabase } from '@/lib/supabase';
import { logger } from '@/lib/logger';

export interface Project {
  id: string;
  title: string;
  description: string;
  lead_id: string;
  status: 'discuss' | 'charter' | 'build' | 'ship';
  tech_stack: string[];
  repo_url?: string;
  demo_url?: string;
  created_at: string;
  updated_at: string;
  profiles?: { full_name: string };
}

export const ProjectService = {
  async getAllProjects() {
    const { data, error } = await supabase
      .from('projects')
      .select('*, profiles:lead_id(full_name)')
      .order('created_at', { ascending: false });

    if (error) {
      logger.error('ProjectService', 'Failed to fetch projects', error);
      throw error;
    }
    return data as Project[];
  },

  async getProjectById(id: string) {
    const { data, error } = await supabase
      .from('projects')
      .select('*, profiles:lead_id(full_name)')
      .eq('id', id)
      .single();

    if (error) {
      logger.error('ProjectService', 'Failed to fetch project', error);
      throw error;
    }
    return data as Project;
  },

  async createProject(project: Partial<Project>) {
    const { data: { user } } = await supabase.auth.getUser();
    if (!user) throw new Error('Unauthorized');

    const { data, error } = await supabase
      .from('projects')
      .insert([{ ...project, lead_id: user.id, status: 'discuss' }])
      .select()
      .single();

    if (error) {
      logger.error('ProjectService', 'Failed to create project', error);
      throw error;
    }
    return data;
  },

  async updateProjectStatus(id: string, status: Project['status']) {
    const { data, error } = await supabase
      .from('projects')
      .update({ status, updated_at: new Date().toISOString() })
      .eq('id', id)
      .select()
      .single();

    if (error) {
      logger.error('ProjectService', 'Failed to update project status', error);
      throw error;
    }
    return data;
  },

  async applyToProject(projectId: string, message: string) {
    const { data: { user } } = await supabase.auth.getUser();
    if (!user) throw new Error('Unauthorized');

    const { data, error } = await supabase
      .from('project_applications')
      .insert([{ project_id: projectId, applicant_id: user.id, message }])
      .select()
      .single();

    if (error) {
      logger.error('ProjectService', 'Failed to apply to project', error);
      throw error;
    }
    return data;
  }
};
