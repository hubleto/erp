<TabPanel header="Billing Accounts">
  <div className="list">

    {R.BILLING_ACCOUNTS && R.BILLING_ACCOUNTS.length > 0
      ? R.BILLING_ACCOUNTS.map((input, key) => {
        var servicesString = "";

        if (input?.SERVICES) {
          input.SERVICES.map((item, index) => {
            if (item.SERVICE?.name) {
              if (index == input.SERVICES.length-1) servicesString += item.SERVICE.name;
              else servicesString += item.SERVICE.name + ", ";
            }
          })
        }

        return (
          <>
            <div className="list-item">
              <button
                onClick={() => { this.setState({highlightIdBussinessAccounts: input.id} as FormCompanyState) }}
                className={"w-full btn-list-item text-left text-sm p-2 hover:bg-gray-50 " + (this.state.highlightIdBussinessAccounts == input.id ? "font-bold bg-gray-50" : "font-medium")}
              >
                <div className="flex grow justify-between">
                  <div className="grow">
                    <span className="break-all">{input.description}<br></br></span>
                    <small className="text text-gray-400">
                      Connected services: {(servicesString != "") ? servicesString : "None"}
                    </small>
                  </div>

                  <div className="flex justify-between gap-2">
                    <span className="icon"
                      onClick={()=> {this.setState({isInlineEditing: true})}}
                    >
                      <i className="fas fa-pencil-alt self-center"></i>
                    </span>
                    <span className="icon"><i className="fas fa-chevron-down self-center"></i></span>
                  </div>
                </div>

              </button>
              {this.state.highlightIdBussinessAccounts == input.id ?
                <div className="card card-body m-2">
                  <FormInput>
                    <div className="grid grid-cols-2 gap-4">
                      <label className="input-label self-center">Billing Account Description</label>
                      <InputVarchar
                        {...this.getDefaultInputProps()}
                        value={input.description}
                        /* isInlineEditing={this.state.isInlineEditingBillingAccounts} */
                        placeholder={globalThis.app.translate(
                          "Billing Account Description"
                        )}
                        onChange={(value: any) => {
                          this.updateRecord({
                            BILLING_ACCOUNTS: { [key]: {description: value} },
                          });
                        }}/>
                    </div>
                  </FormInput>
                </div>
              : null}

              {this.state.highlightIdBussinessAccounts == input.id ?
                <div className="card mx-2 mb-2">
                  <div className="card-header text-sm">Services connected to the Billing Account</div>
                  <div className="card-body">
                    <InputTable
                      uid={this.props.uid + "_table_services_input"}
                      {...this.getDefaultInputProps()}
                      value={R.BILLING_ACCOUNTS[key].SERVICES ?? null}
                      /* isInlineEditing={this.state.isInlineEditingBillingAccounts} */
                      onChange={(value: any) => {
                        this.updateRecord({
                          BILLING_ACCOUNTS: { [key]: {SERVICES: value}
                          },
                        });
                      }}
                    >
                      <TableBillingAccountServices
                        uid={this.props.uid + "_table_services"}
                        context="Hello World"
                        descriptionSource="props"
                        description={{
                          permissions: {
                            canDelete: true,
                            canCreate: true,
                            canRead: true,
                            canUpdate: true,
                          },
                          columns: {
                            id_service: {
                              type: "lookup",
                              title: "Service Name",
                              model: "HubletoApp/Services/Models/Service",
                            },
                          },
                        }}
                      ></TableBillingAccountServices>
                    </InputTable>
                    {this.state.isInlineEditing ? (
                      <a
                        role="button"
                        onClick={() => {
                          if (!R.BILLING_ACCOUNTS[key].SERVICES) R.BILLING_ACCOUNTS[key].SERVICES = [];
                          R.BILLING_ACCOUNTS[key].SERVICES.push({
                            id_billing_account: { _useMasterRecordId_: true },
                          });
                          this.setState({ record: R });
                        }}>
                        + Connect another service
                      </a>
                    ) : null}
                  </div>
                </div>
              : null}
              {this.state.highlightIdBussinessAccounts == input.id && this.state.isInlineEditing ?
                <div className="mx-2 mb-2 flex flex-row justify-end">
                  <button
                    className="btn btn-danger text-sm"
                    onClick={() => {
                      globalThis.app.showDialogDanger(
                        <>This will delete the <b>{input.description}</b> billing account and the connections to the services. Do you want to continue?</>,
                        {
                          header: "Delete billing account",
                          footer: <>
                            <button
                              className="btn btn-danger"
                              onClick={() => {
                                request.get(
                                  'api/record/delete',
                                  {
                                    model: 'HubletoApp/Billing/Models/BillingAccount',
                                    id: input.id,
                                  },
                                  (data: any) => {
                                    if (data.status == true || data.id == 0) {
                                      R.BILLING_ACCOUNTS.splice(key, 1);
                                      this.setState({record: R});
                                      globalThis.app.lastShownDialogRef.current.hide();
                                    }
                                  }
                                );
                              }}
                            >
                              <span className="icon"><i className="fas fa-trash-alt"></i></span>
                              <span className="text">Yes, delete billing account</span>
                            </button>
                            <button
                              className="btn btn-transparent"
                              onClick={() => {
                                globalThis.app.lastShownDialogRef.current.hide();
                              }}
                            >
                              <span className="icon"><i className="fas fa-times"></i></span>
                              <span className="text">No, do not delete billing account</span>
                            </button>
                          </>
                        }
                      );
                    }}
                  >
                    <span className="icon"><i className="fas fa-trash-alt"></i></span>
                    <span className="text">Delete Billing Account</span>
                  </button>
                </div>
              : null}
            </div>
          </>
        )
      })
      : <span className="text-sm p-1">No Billing Accounts</span>
    }
  </div>

  {this.state.isInlineEditing ? (
    <a
      role="button"
      onClick={() => {
        if (!R.BILLING_ACCOUNTS) R.BILLING_ACCOUNTS = [];
        R.BILLING_ACCOUNTS.push({
          id_company: { _useMasterRecordId_: true },
          description: "New Billing Account",
        });
        this.setState({ record: R });
      }}>
      + Add Billing Account
    </a>
  ) : null}
</TabPanel>