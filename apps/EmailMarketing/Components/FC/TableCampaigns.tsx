import React, { Component } from 'react'
import FormCampaign, { FormCampaignProps } from './FormCampaign';
import Table from '@hubleto/react-ui/components/fc/Table';
import { TableMeta, TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces';

const TableCampaigns = (props: TableProps) => {
  return <Table
    componentName='TableCampaigns'
    model='Hubleto/App/Community/EmailMarketing/Models/Campaign'
    formUrlSlug='email-marketing/campaigns'
    getFormModalProps={(table: TableMeta) => {
      return {
        ...table.getDefaultFormModalProps(),
        type: 'right wide',
      };
    }}
    getRowClassName={(table: TableMeta, rowData: any): string => {
      return rowData.is_closed ? 'bg-slate-300' : table.getDefaultRowClassName(rowData);
    }}
    renderCell={(table: TableMeta, columnName: string, column: any, data: any, options: any) => {
      if (columnName == "virt_tags") {
        return data.TAGS.map((tag, key) => {
          return <div key={key} className="text-nowrap mr-2">
            <i style={{color: tag.TAG?.color}} className="fas fa-tag mr-2"></i>
            {tag.TAG?.name}
          </div>;
        });
      } else return table.renderDefaultCell(columnName, column, data, options);
    }}
    renderForm={(table: TableMeta): React.JSX.Element => {
      let formProps = table.getDefaultFormProps() as FormCampaignProps;
      return <FormCampaign {...formProps}/>;
    }}
    {...props}
  ></Table>
}

export default TableCampaigns;

// interface TableCampaignsProps extends TableExtendedProps {}
// interface TableCampaignsState extends TableExtendedState {}

// export default class TableCampaigns extends TableExtended<TableCampaignsProps, TableCampaignsState> {
//   static defaultProps = {
//     ...TableExtended.defaultProps,
//     formUseModalSimple: true,
//     model: 'Hubleto/App/Community/EmailMarketing/Models/Campaign',
//   }

//   props: TableCampaignsProps;
//   state: TableCampaignsState;

//   translationContext: string = 'Hubleto\\App\\Community\\EmailMarketing\\Loader';
//   translationContextInner: string = 'Components\\TableCampaigns';

//   constructor(props: TableCampaignsProps) {
//     super(props);
//     this.state = this.getStateFromProps(props);
//   }

//   getStateFromProps(props: TableCampaignsProps) {
//     return {
//       ...super.getStateFromProps(props),
//     }
//   }

  // getFormModalProps() {
  //   return {
  //     ...super.getFormModalProps(),
  //     type: 'right wide',
  //   };
  // }

  // rowClassName(rowData: any): string {
  //   return rowData.is_closed ? 'bg-slate-300' : super.rowClassName(rowData);
  // }

  // setRecordFormUrl(id: number) {
  //   window.history.pushState({}, "", globalThis.hubleto.config.projectUrl + '/email-marketing/campaigns/' + (id > 0 ? id : 'add'));
  // }

  // renderCell(columnName: string, column: any, data: any, options: any) {
  //   if (columnName == "virt_tags") {
  //     return data.TAGS.map((tag, key) => {
  //       return <div key={key} className="text-nowrap mr-2">
  //         <i style={{color: tag.TAG?.color}} className="fas fa-tag mr-2"></i>
  //         {tag.TAG?.name}
  //       </div>;
  //     });
  //   } else return super.renderCell(columnName, column, data, options);
  // }

//   renderForm(): React.JSX.Element {
//     let formProps = this.getFormProps() as FormCampaignProps;
//     return <FormCampaign {...formProps}/>;
//   }
// }