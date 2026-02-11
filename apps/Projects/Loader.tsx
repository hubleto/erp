import React from 'react';
import App from '@hubleto/react-ui/core/App'
import TableProjects from "./Components/TableProjects"
import TableMilestones from './Components/TableMilestones'
import ProjectsFormActivity from './Components/ProjectsFormActivity'
import request from "@hubleto/react-ui/core/Request";

class ProjectsApp extends App {
  init() {
    super.init();

    // register react components
    globalThis.hubleto.registerReactComponent('ProjectsTableProjects', TableProjects);
    globalThis.hubleto.registerReactComponent('ProjectsTableMilestones', TableMilestones);
    globalThis.hubleto.registerReactComponent('ProjectsFormActivity', ProjectsFormActivity);

    // miscellaneous
    globalThis.hubleto.getApp('Hubleto/App/Community/Orders').addCustomFormTab({
      uid: 'projects',
      title: 'Projects',
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

    globalThis.hubleto.getApp('Hubleto/App/Community/Orders').addFormHeaderButton(
      'Create project',
      (form: any) => {
        request.get(
          'projects/api/create-from-order',
          {idOrder: form.state.record.id},
          (data: any) => {
            if (data.status == "success") {
              globalThis.window.open(globalThis.hubleto.config.projectUrl + '/projects/' + data.idProject);
            }
          }
        );
      }
    )
  }
}

// register app
globalThis.hubleto.registerApp('Hubleto/App/Community/Projects', new ProjectsApp());
