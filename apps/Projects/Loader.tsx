import React from 'react';
import HubletoApp from '@hubleto/react-ui/ext/HubletoApp'
import TableProjects from "./Components/TableProjects"
import TablePhases from './Components/TablePhases'
import request from "@hubleto/react-ui/core/Request";

class ProjectsApp extends HubletoApp {
  init() {
    super.init();

    // register react components
    globalThis.main.registerReactComponent('ProjectsTableProjects', TableProjects);
    globalThis.main.registerReactComponent('ProjectsTablePhases', TablePhases);

    // miscellaneous
    globalThis.main.getApp('HubletoApp/Community/Orders').addFormTab({
      uid: 'projects',
      title: <span className='italic'>Projects</span>,
      onRender: (form: any) => {
        return <TableProjects
          tag={"table_project_order"}
          parentForm={form}
          //@ts-ignore
          description={{ui: {showHeader:false}}}
          descriptionSource='both'
          uid={form.props.uid + "_table_project_order"}
          junctionTitle='Order'
          junctionModel='HubletoApp/Community/Projects/Models/ProjectOrder'
          junctionSourceColumn='id_order'
          junctionSourceRecordId={form.state.record.id}
          junctionDestinationColumn='id_project'
        />;
      },
    });

    globalThis.main.getApp('HubletoApp/Community/Orders').addFormHeaderButton(
      'Create project',
      (form: any) => {
        request.get(
          'projects/api/create-from-order',
          {idOrder: form.state.record.id},
          (data: any) => {
            if (data.status == "success") {
              globalThis.window.open(globalThis.main.config.projectUrl + '/projects/' + data.idProject);
            }
          }
        );
      }
    )
  }
}

// register app
globalThis.main.registerApp('HubletoApp/Community/Projects', new ProjectsApp());
