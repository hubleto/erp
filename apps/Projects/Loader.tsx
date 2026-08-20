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
      globalThis.hubleto.translate('Create project', 'Hubleto\\App\\Community\\Projects\\Loader', 'manifest'),
      'fas fa-diagram-project',
      (form: FormMeta) => {
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
    );

    FormCustomizer.addFormFooterExtraButton(
      'FormTask',
      'Assign task to project',
      'fas fa-check-double',
      (form: any) => {
        globalThis.window.open(globalThis.hubleto.config.projectUrl + '/projects/task-assignment/add?idTask=' + form.state.record.id);
      }
    )
  }
}

// register app
globalThis.hubleto.registerApp('Hubleto/App/Community/Projects', new ProjectsApp());
