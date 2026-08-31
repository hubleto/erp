import React from 'react';
import App from '@hubleto/react-ui/core/App'
import TableProjects from "./Components/FC/TableProjects"
import TableMilestones from './Components/FC/TableMilestones'
import request from "@hubleto/react-ui/core/Request";
import FormCustomizer from '@hubleto/react-ui/core/FormCustomizer';
import { FormMeta } from '@hubleto/react-ui/components/fc/FormInterfaces';
class ProjectsApp extends App {
  init() {
    super.init();

    // register react components
    globalThis.hubleto.registerReactComponent('ProjectsTableProjects', TableProjects);
    globalThis.hubleto.registerReactComponent('ProjectsTableMilestones', TableMilestones);

    // miscellaneous
    globalThis.hubleto.getApp('Hubleto/App/Community/Orders').addCustomFormTab({
      uid: 'projects',
      title: globalThis.hubleto.translate('Projects', 'Hubleto\\App\\Community\\Projects\\Loader', 'manifest'),
      onRender: (form: any) => {
        return <TableProjects
          tag={"table_project_order"}
          parentForm={form}
          //@ts-ignore
          description={{ui: {showHeader:false}}}
          descriptionSource='both'
          uid={form.props.uid + "_table_project_order"}
          junctionTitle='Order'
          junctionModel='Hubleto/App/Community/Projects/Models/ProjectOrder'
          junctionSourceColumn='id_order'
          junctionSourceRecordId={form.state.record.id}
          junctionDestinationColumn='id_project'
        />;
      },
    });

    FormCustomizer.addFormHeaderExtraButton(
      'FormOrder',
      (form: FormMeta) => { return {
        title: 'Create project',
        icon: 'fas fa-diagram-project',
        onClick: (form: FormMeta) => {
          request.get(
            'projects/api/create-from-order',
            {idOrder: form.id},
            (data: any) => {
              if (data.status == "success") {
                globalThis.window.open(globalThis.hubleto.config.projectUrl + '/projects/' + data.idProject);
              }
            }
          );
        }
      }}
    );

    FormCustomizer.addFormHeaderExtraButton(
      'FormTask',
      (form: FormMeta) => { return {
        title: 'Assign task to project',
        icon: 'fas fa-check-double',
        onClick: (form: FormMeta) => {
          globalThis.window.open(globalThis.hubleto.config.projectUrl + '/projects/tasks/add?idTask=' + form.id);
        },
      }},
      (form: FormMeta) => form.id > 0,
    );

    FormCustomizer.addFormHeaderExtraButton(
      'FormTask',
      (form: FormMeta) => { return {
        title: 'Assign task to milestone',
        icon: 'fas fa-check-double',
        onClick: (form: FormMeta) => {
          globalThis.window.open(globalThis.hubleto.config.projectUrl + '/projects/tasks/milestones/add?idTask=' + form.id);
        },
      }},
      (form: FormMeta) => form.id > 0,
    );
  }
}

// register app
globalThis.hubleto.registerApp('Hubleto/App/Community/Projects', new ProjectsApp());
